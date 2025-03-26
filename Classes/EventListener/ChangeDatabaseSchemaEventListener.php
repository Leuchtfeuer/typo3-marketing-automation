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

namespace Bitmotion\MarketingAutomation\EventListener;

use TYPO3\CMS\Core\Database\Event\AlterTableDefinitionStatementsEvent;

class ChangeDatabaseSchemaEventListener
{
    public const PERSONA_ENABLE_FIELDS_KEY = 'tx_marketingautomation_persona';

    private static string $sqlFieldTemplate = 'CREATE TABLE %s ( `%s` varchar(100) DEFAULT \'\' NOT NULL);';

    public function __invoke(AlterTableDefinitionStatementsEvent $event): void
    {
        foreach ($GLOBALS['TCA'] as $table => $config) {
            $personaFieldName = $config['ctrl']['enablecolumns'][self::PERSONA_ENABLE_FIELDS_KEY] ?? '';
            if ($personaFieldName) {
                $sql = sprintf(self::$sqlFieldTemplate, $table, $personaFieldName);
                $event->addSqlData($sql);
            }
        }
    }
}
