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

namespace Bitmotion\MarketingAutomation\Dispatcher;

use Bitmotion\MarketingAutomation\Persona\Persona;

interface SubscriberInterface
{
    public function needsUpdate(Persona $currentPersona, Persona $newPersona): bool;

    public function update(Persona $persona): Persona;
}
