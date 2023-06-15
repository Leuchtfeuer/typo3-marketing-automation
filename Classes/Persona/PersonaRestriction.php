<?php

declare(strict_types=1);

namespace Bitmotion\MarketingAutomation\Persona;

/*
 * This file is part of the "Marketing Automation" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * Team Yoda <dev@Leuchtfeuer.com>, Leuchtfeuer Digital Marketing
 */

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\PageLayoutView;
use TYPO3\CMS\Backend\View\PageLayoutViewDrawFooterHookInterface;
use TYPO3\CMS\Core\Configuration\Event\AfterTcaCompilationEvent;
use TYPO3\CMS\Core\Database\Event\AlterTableDefinitionStatementsEvent;
use TYPO3\CMS\Core\Database\Query\Expression\CompositeExpression;
use TYPO3\CMS\Core\Database\Query\Expression\ExpressionBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\EnforceableQueryRestrictionInterface;
use TYPO3\CMS\Core\Database\Query\Restriction\QueryRestrictionInterface;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Fulfills TYPO3 API to add restriction fields for editors
 * and restricts rendering according to what has been selected
 */
class PersonaRestriction implements SingletonInterface, QueryRestrictionInterface, EnforceableQueryRestrictionInterface, PageLayoutViewDrawFooterHookInterface
{
    public const PERSONA_ENABLE_FIELDS_KEY = 'tx_marketingautomation_persona';

    private static $sqlFieldTemplate = 'CREATE TABLE %s ( `%s` varchar(100) DEFAULT \'\' NOT NULL);';

    private static $tcaFieldTemplate = [
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

    /**
     * @var Persona
     */
    private $persona;

    public function fetchCurrentPersona(Persona $persona): void
    {
        $this->persona = $persona;
    }

    public function isEnforced(): bool
    {
        return $this->isEnabled();
    }

    public function buildExpression(array $queriedTables, ExpressionBuilder $expressionBuilder): CompositeExpression
    {
        $constraints = [];

        if (!$this->isEnabled()) {
            return $expressionBuilder->orX(...$constraints);
        }

        foreach ($queriedTables as $tableAlias => $tableName) {
            $personaFieldName = $GLOBALS['TCA'][$tableName]['ctrl']['enablecolumns'][self::PERSONA_ENABLE_FIELDS_KEY] ?? null;
            if (!empty($personaFieldName)) {
                $fieldName = $tableAlias . '.' . $personaFieldName;
                $constraints = [
                    $expressionBuilder->eq($fieldName, $expressionBuilder->literal('')),
                ];
                $constraints[] = $expressionBuilder->inSet(
                    $fieldName,
                    $expressionBuilder->literal((string)$this->persona->getId())
                );
                if ($this->persona->getId() === 0) {
                    $constraints[] = $expressionBuilder->inSet(
                        $fieldName,
                        $expressionBuilder->literal('-2')
                    );
                }
            }
        }

        return $expressionBuilder->orX(...$constraints);
    }

    private function isEnabled(): bool
    {
        return $this->persona !== null && ($GLOBALS['TYPO3_REQUEST'] ?? null) instanceof ServerRequestInterface
            && ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend();
    }

    public function getPersonaFieldsRequiredDatabaseSchema(AlterTableDefinitionStatementsEvent $event): void
    {
        $additionalSqlString = $this->buildPersonaFieldsRequiredDatabaseSchema();
        foreach ($additionalSqlString as $sql) {
            $event->addSqlData($sql);
        }
    }

    private function buildPersonaFieldsRequiredDatabaseSchema(): array
    {
        $sql = [];

        foreach ($GLOBALS['TCA'] as $table => $config) {
            $personaFieldName = $config['ctrl']['enablecolumns'][self::PERSONA_ENABLE_FIELDS_KEY] ?? '';
            if ($personaFieldName) {
                $sql[] = sprintf(self::$sqlFieldTemplate, $table, $personaFieldName);
            }
        }

        return $sql;
    }

    public function addPersonaRestrictionFieldToTca(AfterTcaCompilationEvent $event): void
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

    /**
     * Modify the cache hash to add persona dimension if applicable
     *
     * @param array &$params Array of parameters: hashParameters, createLockHashBase
     */
    public function addPersonaToCacheIdentifier(&$params): void
    {
        if ($this->persona->isValid()) {
            $params['hashParameters'][self::PERSONA_ENABLE_FIELDS_KEY] = (string)$this->persona->getId();
        }
    }

    public function preProcess(PageLayoutView &$parentObject, &$info, array &$row): void
    {
        $personaFieldName = $GLOBALS['TCA']['tt_content']['ctrl']['enablecolumns'][self::PERSONA_ENABLE_FIELDS_KEY] ?? '';
        if ($personaFieldName === '' || ($row[$personaFieldName] ?? '') === '') {
            return;
        }

        // Unfortunately TYPO3 does not cope with mixed static and relational items, thus we must process them separately
        $staticItems = implode(
            ',',
            array_filter(
                explode(',', $row[$personaFieldName]),
                function ($item) {
                    return $item < 0;
                }
            )
        );
        $relationItems = implode(
            ',',
            array_filter(
                explode(',', $row[$personaFieldName]),
                function ($item) {
                    return $item > 0;
                }
            )
        );
        if ($relationItems) {
            $rowWithRelationItems = $row;
            $rowWithRelationItems[$personaFieldName] = $relationItems;
            $parentObject->getProcessedValue('tt_content', $personaFieldName, $rowWithRelationItems, $info);
            $infoWithRelationItems = array_pop($info);
            $infoWithStaticItems = BackendUtility::getLabelsFromItemsList('tt_content', $personaFieldName, $staticItems);
            if ($infoWithStaticItems) {
                $infoWithRelationItems .= ', ' . $infoWithStaticItems;
            }
            $info[] = $infoWithRelationItems;
        } else {
            $parentObject->getProcessedValue('tt_content', $personaFieldName, $row, $info);
        }
    }
}
