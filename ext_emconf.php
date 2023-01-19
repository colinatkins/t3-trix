<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'RTE Trix for TYPO3 CMS',
    'description' => 'A Trix Rich Text Editor Integration for TYPO3 CMS',
    'category' => 'be',
    'state' => 'stable',
    'author' => 'Colin Atkins',
    'author_email' => 'atkins@hey.com',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '12.0.0',
        ],
        'conflicts' => [],
        'suggests' => [
            'setup' => '12.0.0',
        ],
    ],
];