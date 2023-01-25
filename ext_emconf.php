<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'RTE Trix for TYPO3 CMS',
    'description' => 'A Trix Rich Text Editor Integration for TYPO3 CMS',
    'category' => 'be',
    'state' => 'stable',
    'author' => 'Colin Atkins',
    'author_email' => 'atkins@hey.com',
    'uploadfolder' => '0',
    'version' => '11.0.1',
    'constraints' => [
        'depends' => [
            'typo3' => '11.0.0-11.5.999',
        ],
        'conflicts' => [],
        'suggests' => [
            'typo3' => '11.0.0-11.5.999',
        ],
    ],
];
