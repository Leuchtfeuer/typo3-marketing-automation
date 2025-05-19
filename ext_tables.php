<?php

declare(strict_types=1);

defined('TYPO3') or die();

(function ($extKey): void {
    $GLOBALS['TCA']['tx_marketingautomation_persona']['ctrl']['security']['ignorePageTypeRestriction'] = true;

    // Register some icons
    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
    $iconRegistry->registerIcon(
        'mimetypes-x-tx_marketingautomation_persona',
        TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        [
            'source' => 'EXT:marketing_automation/Resources/Public/Icons/tx_marketingautomation_persona.svg',
        ]
    );

    $iconRegistry->registerIcon(
        'overlay-frontendusers-tx_marketingautomation_persona',
        TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        [
            'source' => 'EXT:marketing_automation/Resources/Public/Icons/overlay-personas.svg',
        ]
    );
})('marketing_automation');
