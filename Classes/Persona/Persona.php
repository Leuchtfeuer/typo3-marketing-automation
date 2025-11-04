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

namespace Leuchtfeuer\MarketingAutomation\Persona;

class Persona
{
    public function __construct(protected int $id, protected int $language) {}

    public function isValid(): bool
    {
        // @extensionScannerIgnoreLine
        return $this->id !== 0;
    }

    public function getId(): int
    {
        // @extensionScannerIgnoreLine
        return $this->id;
    }

    public function getLanguage(): int
    {
        return $this->language;
    }

    public function withId(int $id): self
    {
        $clonedObject = clone $this;
        // @extensionScannerIgnoreLine
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
