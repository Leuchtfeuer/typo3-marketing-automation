<?php

$EM_CONF['marketing_automation'] = [
    'title' => 'Marketing Automation',
    'description' => 'Base TYPO3 extension that allows targeting and personalization of TYPO3 content: Limit pages, content elements etc. to certain "Personas". Determination of Personas can come from various sources (requires add-on extensions).',
    'category' => 'fe',
    'state' => 'stable',
    'version' => '2.1.0',
    'author' => 'Team Yoda',
    'author_company' => 'Leuchtfeuer Digital Marketing',
    'author_email' => 'dev@Leuchtfeuer.com',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-12.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];

