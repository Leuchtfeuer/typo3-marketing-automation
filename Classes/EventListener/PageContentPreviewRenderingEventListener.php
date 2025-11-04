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

/*
 * This file is part of the "Marketing Automation" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * Team Yoda <dev@Leuchtfeuer.com>, Leuchtfeuer Digital Marketing
 */

use Leuchtfeuer\MarketingAutomation\Persona\PersonaRestriction;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\Event\PageContentPreviewRenderingEvent;

/**
 * Event listener for the page layout module to display persona information
 */
class PageContentPreviewRenderingEventListener
{
    private const TABLE_NAME = 'tt_content';

    public function __invoke(PageContentPreviewRenderingEvent $event): void
    {
        $row = $event->getRecord();
        $personaFieldName = $this->getPersonaFieldName();

        if (!$this->hasPersonaRestriction($personaFieldName, $row)) {
            return;
        }

        $personaFieldValue = (string)($row[$personaFieldName] ?? '');
        $content = $this->buildPreviewContent($personaFieldName, $personaFieldValue, $row);

        if ($content !== '') {
            $event->setPreviewContent($event->getPreviewContent() . $content);
        }
    }

    private function getPersonaFieldName(): string
    {
        return $GLOBALS['TCA'][self::TABLE_NAME]['ctrl']['enablecolumns'][PersonaRestriction::PERSONA_ENABLE_FIELDS_KEY] ?? '';
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hasPersonaRestriction(string $personaFieldName, array $row): bool
    {
        return $personaFieldName !== '' && ($row[$personaFieldName] ?? '') !== '';
    }

    /**
     * @param array<string, mixed> $row
     */
    private function buildPreviewContent(string $personaFieldName, string $personaFieldValue, array $row): string
    {
        $staticItems = $this->extractStaticItems($personaFieldValue);
        $relationItems = $this->extractRelationItems($personaFieldValue);

        if ($relationItems !== '') {
            return $this->buildContentWithRelations($personaFieldName, $relationItems, $staticItems, $row);
        }

        return $this->buildContentWithStaticItemsOnly($personaFieldName, $personaFieldValue);
    }

    private function extractStaticItems(string $value): string
    {
        return implode(
            ',',
            array_filter(
                explode(',', $value),
                static fn($item): bool => $item < 0
            )
        );
    }

    private function extractRelationItems(string $value): string
    {
        return implode(
            ',',
            array_filter(
                explode(',', $value),
                static fn($item): bool => $item > 0
            )
        );
    }

    /**
     * @param array<string, mixed> $row
     */
    private function buildContentWithRelations(
        string $personaFieldName,
        string $relationItems,
        string $staticItems,
        array $row
    ): string {
        $fieldLabel = $this->getFieldLabel($personaFieldName);
        $relationContent = $this->getRelationContent($personaFieldName, $relationItems, $row);
        $combinedContent = $this->combineWithStaticItems($personaFieldName, $relationContent, $staticItems);

        return $this->formatContent($fieldLabel, $combinedContent);
    }

    private function buildContentWithStaticItemsOnly(string $personaFieldName, string $personaFieldValue): string
    {
        $fieldLabel = $this->getFieldLabel($personaFieldName);
        $staticContent = BackendUtility::getLabelsFromItemsList(self::TABLE_NAME, $personaFieldName, $personaFieldValue);

        return $this->formatContent($fieldLabel, $staticContent);
    }

    private function getFieldLabel(string $personaFieldName): string
    {
        $fieldLabel = $GLOBALS['TCA'][self::TABLE_NAME]['columns'][$personaFieldName]['label'] ?? $personaFieldName;

        if (is_string($fieldLabel) && str_starts_with($fieldLabel, 'LLL:')) {
            return $GLOBALS['LANG']->sL($fieldLabel);
        }

        return (string)$fieldLabel;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function getRelationContent(string $personaFieldName, string $relationItems, array $row): string
    {
        $rowWithRelationItems = $row;
        $rowWithRelationItems[$personaFieldName] = $relationItems;

        return BackendUtility::getRecordTitlePrep(
            BackendUtility::getProcessedValue(
                self::TABLE_NAME,
                $personaFieldName,
                $rowWithRelationItems[$personaFieldName],
                0,
                false,
                false,
                $rowWithRelationItems['uid']
            )
        );
    }

    private function combineWithStaticItems(
        string $personaFieldName,
        string $relationContent,
        string $staticItems
    ): string {
        if ($staticItems === '') {
            return $relationContent;
        }

        $staticContent = BackendUtility::getLabelsFromItemsList(self::TABLE_NAME, $personaFieldName, $staticItems);

        if ($staticContent === '') {
            return $relationContent;
        }

        if ($relationContent === '') {
            return $staticContent;
        }

        return $relationContent . ', ' . $staticContent;
    }

    private function formatContent(string $fieldLabel, string $content): string
    {
        if ($content === '') {
            return '';
        }

        return '<strong>' . htmlspecialchars($fieldLabel) . '</strong> ' . htmlspecialchars($content);
    }
}
