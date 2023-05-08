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

namespace Bitmotion\MarketingAutomation\Storage;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Security\Cryptography\HashService;
use TYPO3\CMS\Extbase\Security\Exception\InvalidArgumentForHashGenerationException;
use TYPO3\CMS\Extbase\Security\Exception\InvalidHashException;

class Cookie
{
    protected $cookieName = '';

    protected $cookieLifetime = 0;

    /**
     * @var HashService
     */
    protected $hashService;

    public function __construct(string $cookieName, int $cookieLifetime, HashService $hashService = null)
    {
        $this->cookieName = $cookieName;
        $this->cookieLifetime = $cookieLifetime;

        $this->hashService = $hashService ?: GeneralUtility::makeInstance(HashService::class);
    }

    public function read(): array
    {
        try {
            $data = $this->hashService->validateAndStripHmac($_COOKIE[$this->cookieName] ?? '');
        } catch (InvalidArgumentForHashGenerationException $exception) {
            $data = '';
        } catch (InvalidHashException $exception) {
            $data = '';
        }

        return explode('.', rtrim($data, '.'));
    }

    public function save(array $data): void
    {
        setcookie(
            $this->cookieName,
            $this->hashService->appendHmac(implode('.', $data) . '.'),
            time() + $this->cookieLifetime,
            '/',
            '',
            false,
            true
        );
    }
}
