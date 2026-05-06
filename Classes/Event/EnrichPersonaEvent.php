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

namespace Leuchtfeuer\MarketingAutomation\Event;

use Leuchtfeuer\MarketingAutomation\Persona\Persona;

final class EnrichPersonaEvent
{
    public function __construct(
        private readonly Persona $currentPersona,
        private Persona $persona,
    ) {}

    public function getCurrentPersona(): Persona
    {
        return $this->currentPersona;
    }

    public function getPersona(): Persona
    {
        return $this->persona;
    }

    public function setPersona(Persona $persona): void
    {
        $this->persona = $persona;
    }
}
