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

namespace Leuchtfeuer\MarketingAutomation\EventListener;

use Leuchtfeuer\MarketingAutomation\Event\EnrichPersonaEvent;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class EnrichPersonaWithLanguageEventListener
{
    private int $languageId = 0;

    public function __construct()
    {
        try {
            $languageAspect = GeneralUtility::makeInstance(Context::class)->getAspect('language');
            $this->languageId = (int)$languageAspect->getId();
        } catch (\Exception) {
            $this->languageId = 0;
        }
    }

    public function __invoke(EnrichPersonaEvent $event): void
    {
        if (!$this->isValidLanguageId()) {
            $this->languageId = 0;
        }

        $persona = $event->getPersona();
        // @extensionScannerIgnoreLine
        if ($this->languageId === $persona->getLanguage()) {
            return;
        }

        $event->setPersona($persona->withLanguage($this->languageId));
    }

    private function isValidLanguageId(): bool
    {
        if ($this->languageId === 0) {
            return true;
        }

        try {
            $request = $GLOBALS['TYPO3_REQUEST'] ?? null;
            if ($request === null) {
                return false;
            }
            $site = $request->getAttribute('site');
            if ($site === null) {
                return false;
            }
            $site->getLanguageById($this->languageId);
            return true;
        } catch (\InvalidArgumentException) {
            return false;
        }
    }
}
