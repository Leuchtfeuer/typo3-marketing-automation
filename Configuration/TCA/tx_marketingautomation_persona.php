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

return [
    'ctrl' => [
        'title' => 'LLL:EXT:marketing_automation/Resources/Private/Language/locallang_tca.xlf:tx_marketingautomation_persona',
        'descriptionColumn' => 'description',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'sortby' => 'sorting',
        'searchFields' => 'title,description',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'typeicon_classes' => [
            'default' => 'mimetypes-x-tx_marketingautomation_persona',
        ],
    ],
    'types' => [
        '1' => [
            'showitem' => '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,'
                . 'title,'
                . '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,'
                . 'hidden,--palette--;;timeRestriction,'
                . '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,'
                . 'description,'
                . '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
            ',
        ],
    ],
    'palettes' => [
        'timeRestriction' => [
            'showitem' => 'starttime, endtime',
        ],
    ],
    'columns' => [
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'endtime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038),
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'title' => [
            'label' => 'LLL:EXT:marketing_automation/Resources/Private/Language/locallang_tca.xlf:tx_marketingautomation_persona.title',
            'config' => [
                'type' => 'input',
                'width' => 200,
                'eval' => 'trim,required',
            ],
        ],
        'description' => [
            'label' => 'LLL:EXT:marketing_automation/Resources/Private/Language/locallang_tca.xlf:tx_marketingautomation_persona.description',
            'config' => [
                'type' => 'text',
                'default' => '',
            ],
        ],
    ],
];
