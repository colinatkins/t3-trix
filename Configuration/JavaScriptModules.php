<?php

return [
    'dependencies' => [
        'backend',
    ],
    'tags' => [
        'backend.form',
    ],
    'imports' => [
        '@typo3/trix/' => 'EXT:trix/Resources/Public/JavaScript/',
        '@typo3/trix.esm.min.js' => 'EXT:trix/Resources/Public/Contrib/trix.esm.js',
        '@typo3/stimulus.js' => 'EXT:trix/Resources/Public/Contrib/stimulus.js',
    ],
];