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

namespace Leuchtfeuer\MarketingAutomation\Slot;

use Leuchtfeuer\MarketingAutomation\Dispatcher\SubscriberInterface;

use Leuchtfeuer\MarketingAutomation\Persona\Persona;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\Connection;

class LanguageSubscriber implements SubscriberInterface
{
    protected int $languageId = 0;

    public function __construct(private readonly ?ConnectionPool $connectionPool = null)
    {
        try {
            $languageAspect = GeneralUtility::makeInstance(Context::class)->getAspect('language');
            $this->languageId = (int)$languageAspect->getId();
        } catch (\Exception) {
            $this->languageId = 0;
        }
    }

    #[\Override]
    public function needsUpdate(Persona $currentPersona, Persona $newPersona): bool
    {
        if (!$this->isValidLanguageId()) {
            $this->languageId = 0;
        }

        return $this->languageId !== $newPersona->getLanguage();
    }

    #[\Override]
    public function update(Persona $persona): Persona
    {
        return $persona->withLanguage($this->languageId);
    }

    protected function isValidLanguageId(): bool
    {
        if ($this->languageId === 0) {
            return true;
        }

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('sys_language');

        $count = (int)$queryBuilder->count('uid')
                ->from('sys_language')
                ->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($this->languageId, Connection::PARAM_INT)))
                ->executeQuery()
                ->fetchOne();

        return $count === 1;
    }
}
