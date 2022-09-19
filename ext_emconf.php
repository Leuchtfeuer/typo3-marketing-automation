<?php

$EM_CONF['marketing_automation'] = [
    'title' => 'Marketing Automation',
    'description' => 'Base TYPO3 extension that allows targeting and personalization of TYPO3 content: Limit pages, content elements etc. to certain "Personas". Determination of Personas can come from various sources (requires add-on extensions).',
    'category' => 'fe',
    'state' => 'stable',
    'uploadfolder' => false,
    'createDirs' => '',
    'clearCacheOnLoad' => false,
    'version' => '1.2.3',
    'author' => 'Florian Wessels, Helmut Hummel',
    'author_company' => 'Leuchtfeuer Digital Marketing',
    'author_email' => 'dev@Leuchtfeuer.com',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.2-11.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];

