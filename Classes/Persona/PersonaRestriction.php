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

namespace Leuchtfeuer\MarketingAutomation\Persona;

use TYPO3\CMS\Core\Database\Query\Expression\CompositeExpression;
use TYPO3\CMS\Core\Database\Query\Expression\ExpressionBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\EnforceableQueryRestrictionInterface;
use TYPO3\CMS\Core\Database\Query\Restriction\QueryRestrictionInterface;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * Applies persona-related query restrictions in the frontend.
 *
 * Breaking: Former helper methods
 * {@see \Leuchtfeuer\MarketingAutomation\Persona\PersonaRestriction::addPersonaRestrictionFieldToTca()},
 * {@see \Leuchtfeuer\MarketingAutomation\Persona\PersonaRestriction::getPersonaFieldsRequiredDatabaseSchema()},
 * and {@see \Leuchtfeuer\MarketingAutomation\Persona\PersonaRestriction::preProcess()}
 * were moved to dedicated event listeners in marketing_automation v4.1.0.
 */
class PersonaRestriction implements SingletonInterface, QueryRestrictionInterface, EnforceableQueryRestrictionInterface
{
    public const PERSONA_ENABLE_FIELDS_KEY = 'tx_marketingautomation_persona';

    /**
     * @var Persona
     */
    private $persona;

    public function fetchCurrentPersona(Persona $persona): void
    {
        $this->persona = $persona;
    }

    #[\Override]
    public function isEnforced(): bool
    {
        return $this->isEnabled();
    }

    /**
     * @param array<string, string> $queriedTables
     */
    #[\Override]
    public function buildExpression(array $queriedTables, ExpressionBuilder $expressionBuilder): CompositeExpression
    {
        $constraints = [];

        if (!$this->isEnabled()) {
            return $expressionBuilder->or(...$constraints);
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

        return $expressionBuilder->or(...$constraints);
    }

    private function isEnabled(): bool
    {
        return $this->persona !== null && ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend();
    }

    /**
     * Modify the cache hash to add persona dimension if applicable
     *
     * @param array<mixed> &$params Array of parameters: hashParameters, createLockHashBase
     */
    public function addPersonaToCacheIdentifier(&$params): void
    {
        if ($this->persona->isValid()) {
            $params['hashParameters'][self::PERSONA_ENABLE_FIELDS_KEY] = (string)$this->persona->getId();
        }
    }
}
