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

namespace Leuchtfeuer\MarketingAutomation\EventListener;

use Leuchtfeuer\MarketingAutomation\Persona\PersonaRestriction;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\Event\ModifyRecordOverlayIconIdentifierEvent;

class ModifyRecordOverlayIconIdentifierEventListener
{
    public function __invoke(ModifyRecordOverlayIconIdentifierEvent $event): void
    {
        $table = $event->getTable();
        $row = $event->getRow();

        $personaFieldName = $GLOBALS['TCA'][$table]['ctrl']['enablecolumns'][PersonaRestriction::PERSONA_ENABLE_FIELDS_KEY] ?? '';
        $feGroupsFieldName = $GLOBALS['TCA'][$table]['ctrl']['enablecolumns']['fe_group'] ?? '';

        if ($personaFieldName === '' || !empty($event->getStatus()[$feGroupsFieldName])) {
            return;
        }

        $personaFieldValue = $this->resolvePersonaFieldValue($table, $row, $personaFieldName);

        if ($personaFieldValue === '') {
            return;
        }

        $event->setOverlayIconIdentifier('overlay-frontendusers');
    }

    /**
     * @param array<string, mixed> $row
     */
    private function resolvePersonaFieldValue(string $table, array $row, string $personaFieldName): string
    {
        if (array_key_exists($personaFieldName, $row) && $row[$personaFieldName] !== null) {
            $value = $row[$personaFieldName];

            return is_array($value)
                ? $this->normalizePersonaFieldValue($value)
                : trim((string)$value);
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

        $value = $record[$personaFieldName] ?? '';

        return is_array($value)
            ? $this->normalizePersonaFieldValue($value)
            : trim((string)$value);
    }

    /**
     * @param array<int|string, mixed> $value
     */
    private function normalizePersonaFieldValue(array $value): string
    {
        $flattened = [];
        array_walk_recursive(
            $value,
            static function ($item) use (&$flattened): void {
                if ($item !== null && $item !== '') {
                    $flattened[] = (string)$item;
                }
            }
        );

        return implode(',', $flattened);
    }
}
