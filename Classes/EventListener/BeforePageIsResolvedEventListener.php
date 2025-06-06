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

namespace Bitmotion\MarketingAutomation\EventListener;

use Bitmotion\MarketingAutomation\Dispatcher\Dispatcher;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Event\BeforePageIsResolvedEvent;

class BeforePageIsResolvedEventListener
{
    public function __invoke(BeforePageIsResolvedEvent $event): void
    {
        $dispatcher = GeneralUtility::makeInstance(Dispatcher::class);
        $dispatcher->dispatch();
    }
} 