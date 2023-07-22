<?php

/*
 * This file is derived from the official rte_ckeditor extension.
 * All rights belong to their respective owners.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace Atkins\Trix\Form\Resolver;

use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Backend\Form\NodeResolverInterface;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use Atkins\Trix\Form\Element\RichTextElement;

/**
 * This resolver will return the RichTextElement render class if RTE is enabled for this field.
 * @internal This is a specific Backend FormEngine implementation and is not considered part of the Public TYPO3 API.
 */
class RichTextNodeResolver implements NodeResolverInterface
{
    /**
     * Global options from NodeFactory
     *
     * @var array
     */
    protected $data;

    /**
     * Default constructor receives full data array
     */
    public function __construct(NodeFactory $nodeFactory, array $data)
    {
        $this->data = $data;
    }

    /**
     * Returns RichTextElement as class name if RTE widget should be rendered.
     *
     * @return string|null New class name or null if this resolver does not change current class name.
     */
    public function resolve()
    {
        $parameterArray = $this->data['parameterArray'];
        if (// If RTE is enabled for field
            (bool)($parameterArray['fieldConf']['config']['enableRichtext'] ?? false) === true
            // If RTE config is found (prepared by TcaText data provider)
            && is_array($parameterArray['fieldConf']['config']['richtextConfiguration'] ?? null)
            // If RTE is not disabled on configuration level
            && !($parameterArray['fieldConf']['config']['richtextConfiguration']['disabled'] ?? false)
        ) {
            return RichTextElement::class;
        }
        return null;
    }

    protected function getBackendUserAuthentication(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}