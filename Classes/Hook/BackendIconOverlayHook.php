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

namespace Bitmotion\MarketingAutomation\Hook;

use Bitmotion\MarketingAutomation\Persona\PersonaRestriction;

class BackendIconOverlayHook
{
    /**
     * Add a "persona" icon to record items when we have a configuration.
     *
     * @param string  $table    Name of the table to inspect.
     * @param array   $row      The row of the actual element.
     * @param array   $status   The actually status which already is set.
     * @param string  $iconName icon name
     *
     * @return string the registered icon name
     */
    public function postOverlayPriorityLookup(string $table, array $row, array &$status, string $iconName): string
    {
        $personaFieldName = $GLOBALS['TCA'][$table]['ctrl']['enablecolumns'][PersonaRestriction::PERSONA_ENABLE_FIELDS_KEY] ?? '';
        $feGroupsFieldName = $GLOBALS['TCA'][$table]['ctrl']['enablecolumns']['fe_group'] ?? '';

        if (!$personaFieldName || empty($row[$personaFieldName]) || !empty($status[$feGroupsFieldName])) {
            return $iconName;
        }

        $status[PersonaRestriction::PERSONA_ENABLE_FIELDS_KEY] = true;

        return 'overlay-frontendusers';
    }
}
