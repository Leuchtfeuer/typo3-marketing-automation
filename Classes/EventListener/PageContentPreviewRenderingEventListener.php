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
    public function __invoke(PageContentPreviewRenderingEvent $event): void
    {
        $row = $event->getRecord();
        $personaFieldName = $GLOBALS['TCA']['tt_content']['ctrl']['enablecolumns'][PersonaRestriction::PERSONA_ENABLE_FIELDS_KEY] ?? '';

        if ($personaFieldName === '' || ($row[$personaFieldName] ?? '') === '') {
            return;
        }

        // Unfortunately TYPO3 does not cope with mixed static and relational items, thus we must process them separately
        $staticItems = implode(
            ',',
            array_filter(
                explode(',', (string)$row[$personaFieldName]),
                fn($item): bool => $item < 0
            )
        );
        $relationItems = implode(
            ',',
            array_filter(
                explode(',', (string)$row[$personaFieldName]),
                fn($item): bool => $item > 0
            )
        );

        $content = '';
        if ($relationItems !== '' && $relationItems !== '0') {
            $rowWithRelationItems = $row;
            $rowWithRelationItems[$personaFieldName] = $relationItems;

            // Get the label for the field first
            $fieldLabel = $GLOBALS['TCA']['tt_content']['columns'][$personaFieldName]['label'] ?? $personaFieldName;
            if (is_string($fieldLabel) && str_starts_with($fieldLabel, 'LLL:')) {
                $fieldLabel = $GLOBALS['LANG']->sL($fieldLabel);
            }

            // Get the values for relation items
            $relationContent = BackendUtility::getRecordTitlePrep(
                BackendUtility::getProcessedValue(
                    'tt_content',
                    $personaFieldName,
                    $rowWithRelationItems[$personaFieldName],
                    0,
                    false,
                    false,
                    $rowWithRelationItems['uid']
                )
            );

            // Get the values for static items
            $staticContent = '';
            if ($staticItems !== '' && $staticItems !== '0') {
                $staticContent = BackendUtility::getLabelsFromItemsList('tt_content', $personaFieldName, $staticItems);
                if ($staticContent && $relationContent) {
                    $relationContent .= ', ' . $staticContent;
                } elseif ($staticContent) {
                    $relationContent = $staticContent;
                }
            }

            if ($relationContent) {
                $content = '<strong>' . htmlspecialchars((string)$fieldLabel) . '</strong> ' . htmlspecialchars($relationContent);
            }
        } else {
            // For static-only items
            $fieldLabel = $GLOBALS['TCA']['tt_content']['columns'][$personaFieldName]['label'] ?? $personaFieldName;
            if (is_string($fieldLabel) && str_starts_with($fieldLabel, 'LLL:')) {
                $fieldLabel = $GLOBALS['LANG']->sL($fieldLabel);
            }

            $staticContent = BackendUtility::getLabelsFromItemsList('tt_content', $personaFieldName, $row[$personaFieldName]);
            if ($staticContent) {
                $content = '<strong>' . htmlspecialchars((string)$fieldLabel) . '</strong> ' . htmlspecialchars($staticContent);
            }
        }

        if ($content !== '' && $content !== '0') {
            $previewContent = $event->getPreviewContent();
            $event->setPreviewContent($previewContent . $content);
        }
    }
}
