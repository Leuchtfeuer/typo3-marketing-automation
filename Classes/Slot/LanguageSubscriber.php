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

namespace Bitmotion\MarketingAutomation\Slot;

use Bitmotion\MarketingAutomation\Dispatcher\SubscriberInterface;

use Bitmotion\MarketingAutomation\Persona\Persona;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class LanguageSubscriber implements SubscriberInterface
{
    protected $languageId = 0;

    public function __construct()
    {
        try {
            $languageAspect = GeneralUtility::makeInstance(Context::class)->getAspect('language');
            $this->languageId = (int)$languageAspect->getId();
        } catch (\Exception $e) {
            $this->languageId = 0;
        }
    }

    public function needsUpdate(Persona $currentPersona, Persona $newPersona): bool
    {
        if (!$this->isValidLanguageId()) {
            $this->languageId = 0;
        }

        return $this->languageId !== $newPersona->getLanguage();
    }

    public function update(Persona $persona): Persona
    {
        return $persona->withLanguage($this->languageId);
    }

    protected function isValidLanguageId(): bool
    {
        if ($this->languageId === 0) {
            return true;
        }

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_language');

        $count = (int)$queryBuilder->count('uid')
                ->from('sys_language')
                ->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($this->languageId, \PDO::PARAM_INT)))
                ->execute()
                ->fetchColumn();

        return $count === 1;
    }
}
