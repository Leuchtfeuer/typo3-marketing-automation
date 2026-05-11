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

use TYPO3\CMS\Core\Crypto\HashService;
use TYPO3\CMS\Core\Exception\Crypto\InvalidHashStringException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Cookie
{
    protected ?HashService $hashService;

    public function __construct(protected string $cookieName, protected int $cookieLifetime, ?HashService $hashService = null)
    {
        $this->hashService = $hashService ?: GeneralUtility::makeInstance(HashService::class);
    }

    /**
     * @return string[]
     */
    public function read(): array
    {
        try {
            $data = $this->hashService->validateAndStripHmac($_COOKIE[$this->cookieName] ?? '', $this->cookieName);
        } catch (InvalidHashStringException) {
            $data = '';
        }

        return explode('.', rtrim($data, '.'));
    }

    /**
     * @param string[] $data
     */
    public function save(array $data): void
    {
        $isSecure = ($GLOBALS['TYPO3_REQUEST'] ?? null)?->getUri()->getScheme() === 'https';
        setcookie(
            $this->cookieName,
            $this->hashService->appendHmac(implode('.', $data) . '.', $this->cookieName),
            ['expires' => time() + $this->cookieLifetime, 'path' => '/', 'domain' => '', 'secure' => $isSecure, 'httponly' => true]
        );
    }
}
