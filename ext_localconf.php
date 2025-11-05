<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Leuchtfeuer\MarketingAutomation\Dispatcher\Dispatcher;
use Leuchtfeuer\MarketingAutomation\Slot\LanguageSubscriber;
use Leuchtfeuer\MarketingAutomation\Persona\PersonaRestriction;
use Leuchtfeuer\MarketingAutomation\Hook\BackendIconOverlayHook;
use TYPO3\CMS\Core\Imaging\IconFactory;

defined('TYPO3') or die();

(function ($extKey): void {
    $marketingDispatcher = GeneralUtility::makeInstance(Dispatcher::class);
    $marketingDispatcher->addSubscriber(LanguageSubscriber::class);
    $marketingDispatcher->addListener(PersonaRestriction::class . '->fetchCurrentPersona');

    if (!isset($GLOBALS['TYPO3_CONF_VARS']['DB']['additionalQueryRestrictions'][PersonaRestriction::class])) {
        $GLOBALS['TYPO3_CONF_VARS']['DB']['additionalQueryRestrictions'][PersonaRestriction::class] = [];
    }

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['createHashBase'][PersonaRestriction::class] = PersonaRestriction::class . '->addPersonaToCacheIdentifier';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][IconFactory::class]['overrideIconOverlay'][] = BackendIconOverlayHook::class;

})('marketing_automation');
