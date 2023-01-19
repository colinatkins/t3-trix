<?php

use Atkins\Trix\Controller\BrowseLinksController;

return [
    'trix_wizard_browse_links' => [
        'path' => '/trix/wizard/browselinks',
        'target' => BrowseLinksController::class . '::mainAction',
    ],
];