<?php

declare(strict_types=1);

/*
 * This file is part of the "Marketing Automation" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2025 Leuchtfeuer Digital Marketing <dev@leuchtfeuer.com>
 */

namespace Leuchtfeuer\MarketingAutomation\Persona;

use Leuchtfeuer\MarketingAutomation\Dispatcher\Dispatcher;
use Leuchtfeuer\MarketingAutomation\Dispatcher\SubscriberInterface;
use Leuchtfeuer\MarketingAutomation\Event\EnrichPersonaEvent;
use Leuchtfeuer\MarketingAutomation\Storage\Cookie;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class PersonaResolver
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly PersonaRestriction $personaRestriction,
        private readonly ExtensionConfiguration $extensionConfiguration,
        private readonly Dispatcher $legacyDispatcher,
    ) {}

    public function resolve(): void
    {
        $config = $this->getExtensionConfiguration();
        $cookie = GeneralUtility::makeInstance(
            Cookie::class,
            $config['cookieName'] ?? '',
            (int)($config['cookieLifetime'] ?? 0),
        );
        $data = $cookie->read();
        $id = (int)($data[0] ?? 0);
        $language = (int)($data[1] ?? -1);
        $currentPersona = GeneralUtility::makeInstance(Persona::class, $id, $language);

        $event = new EnrichPersonaEvent($currentPersona, $currentPersona);
        $this->eventDispatcher->dispatch($event);
        $newPersona = $event->getPersona();

        foreach ($this->legacyDispatcher->getSubscribers() as $subscriberClass) {
            if (!class_exists($subscriberClass)) {
                throw new \RuntimeException(sprintf('Class %s does not exist.', $subscriberClass), 1587540937);
            }
            $subscriber = GeneralUtility::makeInstance($subscriberClass);
            if (!$subscriber instanceof SubscriberInterface) {
                throw new \RuntimeException(sprintf('Class %s needs to implement %s.', $subscriberClass, SubscriberInterface::class), 1530273364);
            }
            if ($subscriber->needsUpdate($currentPersona, $newPersona)) {
                $newPersona = $subscriber->update($newPersona);
            }
        }

        if ($currentPersona !== $newPersona) {
            $cookie->save([
                (string)$newPersona->getId(),
                // @extensionScannerIgnoreLine
                (string)$newPersona->getLanguage(),
            ]);
        }

        $this->personaRestriction->fetchCurrentPersona($newPersona);

        foreach ($this->legacyDispatcher->getListeners() as $listener) {
            $ref = null;
            GeneralUtility::callUserFunction($listener, $newPersona, $ref);
        }
    }

    /**
     * @return array<mixed>
     */
    private function getExtensionConfiguration(): array
    {
        try {
            return $this->extensionConfiguration->get('marketing_automation');
        } catch (\Exception) {
            return [];
        }
    }
}
