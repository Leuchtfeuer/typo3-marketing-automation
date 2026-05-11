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

namespace Leuchtfeuer\MarketingAutomation\Dispatcher;

use Leuchtfeuer\MarketingAutomation\Persona\PersonaResolver;
use Leuchtfeuer\MarketingAutomation\Persona\PersonaRestriction;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @deprecated since v13, will be removed in v14. Use the PSR-14 event
 * {@see \Leuchtfeuer\MarketingAutomation\Event\EnrichPersonaEvent} instead.
 * Subscribers registered via {@see addSubscriber()} continue to work via a
 * compatibility bridge in {@see PersonaResolver} but should be migrated to
 * regular PSR-14 event listeners.
 */
class Dispatcher implements SingletonInterface
{
    /**
     * @var string[]
     */
    protected array $subscribers = [];

    /**
     * @var string[]
     */
    protected array $listeners = [];

    /**
     * @deprecated since v13, will be removed in v14. Register a PSR-14 listener
     * for {@see \Leuchtfeuer\MarketingAutomation\Event\EnrichPersonaEvent} instead.
     */
    public function addSubscriber(string $className): void
    {
        $this->subscribers[] = $className;
    }

    /**
     * @deprecated since v13, will be removed in v14. Register a PSR-14 listener instead.
     */
    public function addListener(string $className): void
    {
        $this->listeners[] = $className;
    }

    /**
     * @return string[]
     * @internal Used by {@see PersonaResolver} to apply legacy subscribers.
     */
    public function getSubscribers(): array
    {
        return $this->subscribers;
    }

    /**
     * @return string[]
     * @internal Used by {@see PersonaResolver} to apply legacy callback listeners.
     */
    public function getListeners(): array
    {
        return $this->listeners;
    }

    /**
     * @deprecated since v13, will be removed in v14. The persona resolution is
     * now triggered automatically via the PSR-14 event flow in
     * {@see \Leuchtfeuer\MarketingAutomation\EventListener\BeforePageIsResolvedEventListener}.
     */
    public function dispatch(PersonaRestriction $personaRestriction): void
    {
        GeneralUtility::makeInstance(PersonaResolver::class)->resolve();
    }
}
