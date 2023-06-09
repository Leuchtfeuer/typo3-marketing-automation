<?php

declare(strict_types=1);

defined('TYPO3') or die();

(function ($extKey): void {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['determineId-PreProcessing']['marketing_automation'] =
        \Bitmotion\MarketingAutomation\Dispatcher\Dispatcher::class . '->dispatch';

    $marketingDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Bitmotion\MarketingAutomation\Dispatcher\Dispatcher::class);
    $marketingDispatcher->addSubscriber(\Bitmotion\MarketingAutomation\Slot\LanguageSubscriber::class);
    $marketingDispatcher->addListener(\Bitmotion\MarketingAutomation\Persona\PersonaRestriction::class . '->fetchCurrentPersona');

    if (!isset($GLOBALS['TYPO3_CONF_VARS']['DB']['additionalQueryRestrictions'][\Bitmotion\MarketingAutomation\Persona\PersonaRestriction::class])) {
        $GLOBALS['TYPO3_CONF_VARS']['DB']['additionalQueryRestrictions'][\Bitmotion\MarketingAutomation\Persona\PersonaRestriction::class] = [];
    }

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['createHashBase'][\Bitmotion\MarketingAutomation\Persona\PersonaRestriction::class] = \Bitmotion\MarketingAutomation\Persona\PersonaRestriction::class . '->addPersonaToCacheIdentifier';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][\TYPO3\CMS\Core\Imaging\IconFactory::class]['overrideIconOverlay'][] = \Bitmotion\MarketingAutomation\Hook\BackendIconOverlayHook::class;

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawFooter'][\Bitmotion\MarketingAutomation\Persona\PersonaRestriction::class] = \Bitmotion\MarketingAutomation\Persona\PersonaRestriction::class;
})('marketing_automation');
