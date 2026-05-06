<?php

declare(strict_types=1);

use Leuchtfeuer\MarketingAutomation\Persona\PersonaRestriction;

defined('TYPO3') or die();

$GLOBALS['TYPO3_CONF_VARS']['DB']['additionalQueryRestrictions'][PersonaRestriction::class] ??= [];
