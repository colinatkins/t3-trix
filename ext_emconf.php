<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'RTE Trix for TYPO3 CMS',
    'description' => 'A Trix Rich Text Editor Integration for TYPO3 CMS',
    'category' => 'be',
    'state' => 'stable',
    'author' => 'Colin Atkins',
    'author_email' => 'atkins@hey.com',
    'uploadfolder' => '0',
    'version' => '12.0.2',
    'constraints' => [
        'depends' => [
            'typo3' => '12.0.0-12.1.999',
        ],
        'conflicts' => [],
        'suggests' => [
            'setup' => '12.0.0-12.1.999',
        ],
    ],
];