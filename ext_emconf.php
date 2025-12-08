<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'RTE Trix for TYPO3 CMS',
    'description' => 'A Trix Rich Text Editor Integration for TYPO3 CMS',
    'category' => 'be',
    'state' => 'stable',
    'author' => 'Colin Atkins',
    'author_email' => 'atkins@hey.com',
    'uploadfolder' => '0',
    'version' => '14.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '14.0.0-14.99.999',
        ],
        'conflicts' => [],
        'suggests' => [
            'setup' => '14.0.0-14.99.999',
        ],
    ],
];