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

namespace Leuchtfeuer\MarketingAutomation\Hook;

use Leuchtfeuer\MarketingAutomation\Persona\PersonaRestriction;
use TYPO3\CMS\Backend\Utility\BackendUtility;

class BackendIconOverlayHook
{
    /**
     * Add a "persona" icon to record items when we have a configuration.
     *
     * @param string  $table    Name of the table to inspect.
     * @param array<string, mixed>   $row      The row of the actual element.
     * @param array<string, mixed>   $status   The actually status which already is set.
     * @param string  $iconName icon name
     *
     * @return string the registered icon name
     */
    public function postOverlayPriorityLookup(string $table, array $row, array &$status, string $iconName): string
    {
        $personaFieldName = $GLOBALS['TCA'][$table]['ctrl']['enablecolumns'][PersonaRestriction::PERSONA_ENABLE_FIELDS_KEY] ?? '';
        $feGroupsFieldName = $GLOBALS['TCA'][$table]['ctrl']['enablecolumns']['fe_group'] ?? '';

        if ($personaFieldName === '' || !empty($status[$feGroupsFieldName])) {
            return $iconName;
        }

        $personaFieldValue = $this->resolvePersonaFieldValue($table, $row, $personaFieldName);

        if ($personaFieldValue === '') {
            return $iconName;
        }

        $status[PersonaRestriction::PERSONA_ENABLE_FIELDS_KEY] = true;

        return 'overlay-frontendusers';
    }

    private function resolvePersonaFieldValue(string $table, array $row, string $personaFieldName): string
    {
        if (array_key_exists($personaFieldName, $row) && $row[$personaFieldName] !== null) {
            return (string)$row[$personaFieldName];
        }

        $uid = (int)($row['uid'] ?? 0);
        if ($uid <= 0) {
            return '';
        }

        $record = BackendUtility::getRecord(
            $table,
            $uid,
            $personaFieldName
        );

        if (!is_array($record) || !array_key_exists($personaFieldName, $record)) {
            return '';
        }

        return (string)($record[$personaFieldName] ?? '');
    }
}
