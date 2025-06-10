<?php

declare(strict_types=1);

defined('TYPO3') or die();

(function ($extKey): void {
    $marketingDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Leuchtfeuer\MarketingAutomation\Dispatcher\Dispatcher::class);
    $marketingDispatcher->addSubscriber(\Leuchtfeuer\MarketingAutomation\Slot\LanguageSubscriber::class);
    $marketingDispatcher->addListener(\Leuchtfeuer\MarketingAutomation\Persona\PersonaRestriction::class . '->fetchCurrentPersona');

    if (!isset($GLOBALS['TYPO3_CONF_VARS']['DB']['additionalQueryRestrictions'][\Leuchtfeuer\MarketingAutomation\Persona\PersonaRestriction::class])) {
        $GLOBALS['TYPO3_CONF_VARS']['DB']['additionalQueryRestrictions'][\Leuchtfeuer\MarketingAutomation\Persona\PersonaRestriction::class] = [];
    }

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['createHashBase'][\Leuchtfeuer\MarketingAutomation\Persona\PersonaRestriction::class] = \Leuchtfeuer\MarketingAutomation\Persona\PersonaRestriction::class . '->addPersonaToCacheIdentifier';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][\TYPO3\CMS\Core\Imaging\IconFactory::class]['overrideIconOverlay'][] = \Leuchtfeuer\MarketingAutomation\Hook\BackendIconOverlayHook::class;

})('marketing_automation');
