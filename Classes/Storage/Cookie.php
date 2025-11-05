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

namespace Leuchtfeuer\MarketingAutomation\Storage;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Security\Cryptography\HashService;
use TYPO3\CMS\Extbase\Security\Exception\InvalidArgumentForHashGenerationException;
use TYPO3\CMS\Extbase\Security\Exception\InvalidHashException;

class Cookie
{
    protected HashService $hashService;

    public function __construct(protected string $cookieName, protected int $cookieLifetime, HashService $hashService = null)
    {
        $this->hashService = $hashService ?: GeneralUtility::makeInstance(HashService::class);
    }

    /**
     * @return string[]
     */
    public function read(): array
    {
        try {
            $data = $this->hashService->validateAndStripHmac($_COOKIE[$this->cookieName] ?? '');
        } catch (InvalidArgumentForHashGenerationException|InvalidHashException) {
            $data = '';
        }

        return explode('.', rtrim($data, '.'));
    }

    /**
     * @param string[] $data
     */
    public function save(array $data): void
    {
        setcookie(
            $this->cookieName,
            $this->hashService->appendHmac(implode('.', $data) . '.'),
            ['expires' => time() + $this->cookieLifetime, 'path' => '/', 'domain' => '', 'secure' => false, 'httponly' => true]
        );
    }
}
