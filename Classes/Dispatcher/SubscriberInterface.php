<?php

declare(strict_types=1);

namespace Bitmotion\MarketingAutomation\Dispatcher;

/*
 * This file is part of the "Marketing Automation" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * Team Yoda <dev@Leuchtfeuer.com>, Leuchtfeuer Digital Marketing
 */

use Bitmotion\MarketingAutomation\Persona\Persona;

interface SubscriberInterface
{
    public function needsUpdate(Persona $currentPersona, Persona $newPersona): bool;

    public function update(Persona $persona): Persona;
}
