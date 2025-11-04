<?php

/*
 * This file is part of the "Marketing Automation" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2025 Leuchtfeuer Digital Marketing <dev@leuchtfeuer.com>
 */

namespace Leuchtfeuer\MarketingAutomation\EventListener;

use TYPO3\CMS\Core\Configuration\Event\AfterTcaCompilationEvent;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class AddTcaPersonaRestrictionFieldEventListener
{
    public const PERSONA_ENABLE_FIELDS_KEY = 'tx_marketingautomation_persona';

    /**
     * @var array<string, mixed>
     */
    private static array $tcaFieldTemplate = [
        'label' => 'LLL:EXT:marketing_automation/Resources/Private/Language/locallang_tca.xlf:tx_marketingautomation_persona_restriction.label',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectMultipleSideBySide',
            'exclusiveKeys' => '-1',
            'foreign_table' => 'tx_marketingautomation_persona',
            'foreign_table_where' => 'ORDER BY tx_marketingautomation_persona.title',
            'items' => [
                [
                    'LLL:EXT:marketing_automation/Resources/Private/Language/locallang_tca.xlf:tx_marketingautomation_persona_restriction.hideWhenNoMatch',
                    -1,
                ],
                [
                    'LLL:EXT:marketing_automation/Resources/Private/Language/locallang_tca.xlf:tx_marketingautomation_persona_restriction.showWhenNoMatch',
                    -2,
                ],
                [
                    'LLL:EXT:marketing_automation/Resources/Private/Language/locallang_tca.xlf:tx_marketingautomation_persona_restriction.personaItemSeparator',
                    '--div--',
                ],
            ],
        ],
    ];

    public function __invoke(AfterTcaCompilationEvent $event): void
    {
        $tca = $event->getTca();
        foreach ($tca as $table => &$config) {
            $personaFieldName = $config['ctrl']['enablecolumns'][self::PERSONA_ENABLE_FIELDS_KEY] ?? '';
            if ($personaFieldName) {
                $config['columns'][$personaFieldName] = array_replace_recursive(
                    self::$tcaFieldTemplate,
                    $config['columns'][$personaFieldName] ?? []
                );
                // Expose current config to globals TCA, make the below TYPO3 API work, which works on globals
                $GLOBALS['TCA'][$table] = &$config;
                ExtensionManagementUtility::addToAllTCAtypes(
                    $table,
                    $personaFieldName,
                    '',
                    'after:fe_group'
                );
                // Remove the global exposure we created above
                unset($GLOBALS['TCA'][$table]);
            }
        }
        $event->setTca($tca);

    }
}
