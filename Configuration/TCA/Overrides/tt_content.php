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

use Bitmotion\MarketingAutomation\Persona\PersonaRestriction;

$GLOBALS['TCA']['tt_content']['ctrl']['enablecolumns'][PersonaRestriction::PERSONA_ENABLE_FIELDS_KEY] = 'tx_marketingautomation_persona';
