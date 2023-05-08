<?php

declare(strict_types=1);

/*
 * This file is part of the "Marketing Automation" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * Team Yoda <dev@Leuchtfeuer.com>, Leuchtfeuer Digital Marketing
 */

namespace Bitmotion\MarketingAutomation\Dispatcher;

use Bitmotion\MarketingAutomation\Persona\Persona;
use Bitmotion\MarketingAutomation\Storage\Cookie;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Dispatcher implements SingletonInterface
{
    protected $subscribers = [];

    protected $listeners = [];

    public function addSubscriber(string $className): void
    {
        $this->subscribers[] = $className;
    }

    public function addListener(string $className): void
    {
        $this->listeners[] = $className;
    }

    public function dispatch(): void
    {
        $extensionConfiguration = $this->getExtensionConfiguration();
        $storage = GeneralUtility::makeInstance(Cookie::class, $extensionConfiguration['cookieName'], (int)$extensionConfiguration['cookieLifetime']);
        $data = $storage->read();
        $id = (int)($data[0] ?? 0);
        $language = (int)($data[1] ?? -1);
        $currentPersona = $newPersona = GeneralUtility::makeInstance(Persona::class, $id, $language);

        foreach ($this->subscribers as $subscriber) {
            if (!class_exists($subscriber)) {
                throw new \RuntimeException(sprintf('Class %s does not exist.', $subscriber), 1587540937);
            }

            $object = GeneralUtility::makeInstance($subscriber);

            if (!$object instanceof SubscriberInterface) {
                throw new \RuntimeException(sprintf('Class %s needs to implement %s.', $subscriber, SubscriberInterface::class), 1530273364);
            }

            if ($object->needsUpdate($currentPersona, $newPersona)) {
                $newPersona = $object->update($newPersona);
            }
        }

        if ($currentPersona !== $newPersona) {
            $storage->save([
                $newPersona->getId(),
                $newPersona->getLanguage(),
            ]);
        }

        foreach ($this->listeners as $listener) {
            $ref = null;
            GeneralUtility::callUserFunction($listener, $newPersona, $ref);
        }
    }

    protected function getExtensionConfiguration(): array
    {
        try {
            return GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('marketing_automation');
        } catch (\Exception $e) {
            return [];
        }
    }
}
