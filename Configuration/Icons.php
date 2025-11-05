<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    'mimetypes-x-tx_marketingautomation_persona' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:marketing_automation/Resources/Public/Icons/tx_marketingautomation_persona.svg',
    ],
    'overlay-frontendusers-tx_marketingautomation_persona' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:marketing_automation/Resources/Public/Icons/overlay-personas.svg',
    ],
];
