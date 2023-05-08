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

namespace Bitmotion\MarketingAutomation\Persona;

class Persona
{
    protected $id = 0;

    protected $language = 0;

    public function __construct(int $id, int $language)
    {
        $this->id = $id;
        $this->language = $language;
    }

    public function isValid(): bool
    {
        return $this->id !== 0;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLanguage(): int
    {
        return $this->language;
    }

    public function withId(int $id): self
    {
        $clonedObject = clone $this;
        $clonedObject->id = $id;

        return $clonedObject;
    }

    public function withLanguage(int $language): self
    {
        $clonedObject = clone $this;
        $clonedObject->language = $language;

        return $clonedObject;
    }
}
