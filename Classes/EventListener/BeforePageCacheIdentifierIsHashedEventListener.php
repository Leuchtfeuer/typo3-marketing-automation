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
use TYPO3\CMS\Frontend\Event\BeforePageCacheIdentifierIsHashedEvent;

class BeforePageCacheIdentifierIsHashedEventListener
{
    public function __construct(private readonly PersonaRestriction $personaRestriction) {}

    public function __invoke(BeforePageCacheIdentifierIsHashedEvent $event): void
    {
        $persona = $this->personaRestriction->getCurrentPersona();
        if ($persona !== null && $persona->isValid()) {
            $params = $event->getPageCacheIdentifierParameters();
            $params[PersonaRestriction::PERSONA_ENABLE_FIELDS_KEY] = (string)$persona->getId();
            $event->setPageCacheIdentifierParameters($params);
        }
    }
}
