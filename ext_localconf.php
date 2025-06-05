<?php

declare(strict_types=1);

defined('TYPO3') or die();

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeResolver'][1480314091] = [
    'nodeName' => 'text',
    'priority' => 50,
    'class' => \Atkins\Trix\Form\Resolver\RichTextNodeResolver::class,
];

if (empty($GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['default'])) {
    $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['default'] = 'EXT:trix/Configuration/RTE/Default.yaml';
}