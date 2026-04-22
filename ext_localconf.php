<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Leuchtfeuer\MarketingAutomation\Dispatcher\Dispatcher;
use Leuchtfeuer\MarketingAutomation\Slot\LanguageSubscriber;
use Leuchtfeuer\MarketingAutomation\Persona\PersonaRestriction;

defined('TYPO3') or die();

(function ($extKey): void {
    $marketingDispatcher = GeneralUtility::makeInstance(Dispatcher::class);
    $marketingDispatcher->addSubscriber(LanguageSubscriber::class);

    if (!isset($GLOBALS['TYPO3_CONF_VARS']['DB']['additionalQueryRestrictions'][PersonaRestriction::class])) {
        $GLOBALS['TYPO3_CONF_VARS']['DB']['additionalQueryRestrictions'][PersonaRestriction::class] = [];
    }


})('marketing_automation');
