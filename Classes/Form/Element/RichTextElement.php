<?php

declare(strict_types=1);

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

namespace Atkins\Trix\Form\Element;

use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Localization\Locales;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\RteCKEditor\Controller\ResourceController;

/**
 * Render rich text editor in FormEngine
 * @internal This is a specific Backend FormEngine implementation and is not considered part of the Public TYPO3 API.
 */
class RichTextElement extends AbstractFormElement
{
    /**
     * Default field information enabled for this element.
     *
     * @var array
     */
    protected $defaultFieldInformation = [
        'tcaDescription' => [
            'renderType' => 'tcaDescription',
        ],
    ];

    /**
     * Default field wizards enabled for this element.
     *
     * @var array
     */
    protected $defaultFieldWizard = [
        'localizationStateSelector' => [
            'renderType' => 'localizationStateSelector',
        ],
        'otherLanguageContent' => [
            'renderType' => 'otherLanguageContent',
            'after' => [
                'localizationStateSelector',
            ],
        ],
        'defaultLanguageDifferences' => [
            'renderType' => 'defaultLanguageDifferences',
            'after' => [
                'otherLanguageContent',
            ],
        ],
    ];

    /**
     * This property contains configuration related to the RTE
     * But only the .editor configuration part
     *
     * @var array
     */
    protected $rteConfiguration = [];

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * Container objects give $nodeFactory down to other containers.
     *
     * @param EventDispatcherInterface|null $eventDispatcher
     */
    public function __construct(NodeFactory $nodeFactory, array $data, EventDispatcherInterface $eventDispatcher = null)
    {
        parent::__construct($nodeFactory, $data);
        $this->eventDispatcher = $eventDispatcher ?? GeneralUtility::makeInstance(EventDispatcherInterface::class);
    }

    /**
     * Renders the ckeditor element
     *
     * @throws \InvalidArgumentException
     */
    public function render(): array
    {
        $resultArray = $this->initializeResultArray();
        $parameterArray = $this->data['parameterArray'];
        $config = $parameterArray['fieldConf']['config'];

        $fieldId = $this->sanitizeFieldId($parameterArray['itemFormElName']);
        $itemFormElementName = $this->data['parameterArray']['itemFormElName'];

        $value = $this->data['parameterArray']['itemFormElValue'] ?? '';

        $fieldInformationResult = $this->renderFieldInformation();
        $fieldInformationHtml = $fieldInformationResult['html'];
        $resultArray = $this->mergeChildReturnIntoExistingResult($resultArray, $fieldInformationResult, false);

        $fieldControlResult = $this->renderFieldControl();
        $fieldControlHtml = $fieldControlResult['html'];
        $resultArray = $this->mergeChildReturnIntoExistingResult($resultArray, $fieldControlResult, false);

        $fieldWizardResult = $this->renderFieldWizard();
        $fieldWizardHtml = $fieldWizardResult['html'];
        $resultArray = $this->mergeChildReturnIntoExistingResult($resultArray, $fieldWizardResult, false);

        $this->rteConfiguration = $config['richtextConfiguration']['editor'] ?? [];

        $editorId = $fieldId . 'trix';

        $trixControllerAttributes = GeneralUtility::implodeAttributes([
            'data-controller' => 'trix',
            'data-trix-link-browser-url-value' => $this->getLinkBrowserRoute(),
            'data-trix-heading-tagname-value' => $this->headingTagName()
        ], true);

        $hiddenTagAttributes = GeneralUtility::implodeAttributes([
            'type' => 'hidden',
            'name' => $itemFormElementName,
            'id' => $editorId,
            'value' => $value
        ], true);

        $trixTagAttributes = GeneralUtility::implodeAttributes([
            'input' => $editorId,
            'class' => 'trix-content',
            'data-trix-target' => 'editor',
            'autofocus' => $this->hasAutofocus()
        ], true);

        $html = [];
        $html[] = '<div class="formengine-field-item t3js-formengine-field-item">';
        $html[] =   $fieldInformationHtml;
        $html[] =   '<div class="form-control-wrap">';
        $html[] =       '<div class="form-wizards-wrap">';
        $html[] =           '<div class="form-wizards-element" ' . $trixControllerAttributes . '>';
        $html[] =               '<input ' . $hiddenTagAttributes . '>';
        $html[] =               '<trix-editor ' . $trixTagAttributes . '></trix-editor>';
        $html[] =           '</div>';

        if (!empty($fieldControlHtml)) {
            $html[] =           '<div class="form-wizards-items-aside form-wizards-items-aside--field-control">';
            $html[] =               '<div class="btn-group">';
            $html[] =                   $fieldControlHtml;
            $html[] =               '</div>';
            $html[] =           '</div>';
        }
        if (!empty($fieldWizardHtml)) {
            $html[] = '<div class="form-wizards-items-bottom">';
            $html[] = $fieldWizardHtml;
            $html[] = '</div>';
        }

        $html[] =       '</div>';
        $html[] =   '</div>';
        $html[] = '</div>';

        $resultArray['html'] = implode(LF, $html);
        $resultArray['javaScriptModules'][] = JavaScriptModuleInstruction::create('@typo3/trix/trix.js');
        $resultArray['stylesheetFiles'][] = PathUtility::getPublicResourceWebPath('EXT:trix/Resources/Public/Contrib/trix.css');
        $resultArray['stylesheetFiles'][] = PathUtility::getPublicResourceWebPath('EXT:trix/Resources/Public/Css/editor.css');

        $styleSrc = (string)($trixEditorConfiguration['options']['contentsCss'] ?? '');
        if ($styleSrc !== '') {
            $resultArray['stylesheetFiles'][] = PathUtility::getPublicResourceWebPath($styleSrc);
        }

        return $resultArray;
    }

    private function hasAutofocus(): bool {
        return (string)($this->resolveTrixEditorConfiguration()['options']['autofocus'] ?? '') == 'true';
    }

    private function headingTagName(): string {
        return (string)($this->resolveTrixEditorConfiguration()['options']['heading']['tagName'] ?? 'h3');
    }

    /**
     * Determine the contents language iso code
     */
    protected function getLanguageIsoCodeOfContent(): string
    {
        $currentLanguageUid = ($this->data['databaseRow']['sys_language_uid'] ?? 0);
        if (is_array($currentLanguageUid)) {
            $currentLanguageUid = $currentLanguageUid[0];
        }
        $contentLanguageUid = (int)max($currentLanguageUid, 0);
        if ($contentLanguageUid) {
            // the language rows might not be fully inialized, so we fallback to en_US in this case
            $contentLanguage = $this->data['systemLanguageRows'][$currentLanguageUid]['iso'] ?? 'en_US';
        } else {
            $contentLanguage = $this->rteConfiguration['config']['defaultContentLanguage'] ?? 'en_US';
        }

        $languageCode = '';

        if (str_contains($contentLanguage, '_')) {
            $languageCodeParts = explode('_', $contentLanguage);
            $languageCode = $languageCodeParts[1];
        } else if(strlen($contentLanguage) == 2) {
            $languageCode = $contentLanguage;
        } else {
            $contentLanguage = 'en';
        }
        $contentLanguage = '_' . strtoupper($languageCode);
        // Find the configured language in the list of localization locales
        $locales = GeneralUtility::makeInstance(Locales::class);
        // If not found, default to 'en'
        if (!in_array($contentLanguage, $locales->getLocales(), true)) {
            $contentLanguage = 'en';
        }
        return $contentLanguage;
    }

    /**
     * Determine the language direction
     */
    protected function getLanguageDirectionOfContent(): string
    {
        $currentLanguageUid = ($this->data['databaseRow']['sys_language_uid'] ?? 0);
        if (is_array($currentLanguageUid)) {
            $currentLanguageUid = $currentLanguageUid[0];
        }
        $contentLanguageUid = (int)max($currentLanguageUid, 0);
        return $this->data['systemLanguageRows'][$contentLanguageUid]['direction'] ?? '';
    }

    /**
     * @return array{options: array, externalPlugins: array}
     */
    protected function resolveTrixEditorConfiguration(): array
    {
        $configuration = $this->prepareConfigurationForEditor();

        return [
            'options' => $configuration
        ];
    }



    /**
     * Get configuration of external/additional plugins
     */
    protected function getLinkBrowserRoute(): string
    {
        $urlParameters = [
            'P' => [
                'table'      => $this->data['tableName'],
                'uid'        => $this->data['databaseRow']['uid'],
                'fieldName'  => $this->data['fieldName'],
                'recordType' => $this->data['recordTypeValue'],
                'pid'        => $this->data['effectivePid'],
                'richtextConfigurationName' => $this->data['parameterArray']['fieldConf']['config']['richtextConfigurationName'],
            ],
        ];

        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);

        return (string)$uriBuilder->buildUriFromRoute('trix_wizard_browse_links', $urlParameters);
    }

    /**
     * Add configuration to replace LLL: references with the translated value
     */
    protected function replaceLanguageFileReferences(array $configuration): array
    {
        foreach ($configuration as $key => $value) {
            if (is_array($value)) {
                $configuration[$key] = $this->replaceLanguageFileReferences($value);
            } elseif (is_string($value)) {
                $configuration[$key] = $this->getLanguageService()->sL($value);
            }
        }
        return $configuration;
    }

    /**
     * Add configuration to replace absolute EXT: paths with relative ones
     */
    protected function replaceAbsolutePathsToRelativeResourcesPath(array $configuration): array
    {
        foreach ($configuration as $key => $value) {
            if (is_array($value)) {
                $configuration[$key] = $this->replaceAbsolutePathsToRelativeResourcesPath($value);
            } elseif (is_string($value) && PathUtility::isExtensionPath(strtoupper($value))) {
                $configuration[$key] = $this->resolveUrlPath($value);
            }
        }
        return $configuration;
    }

    /**
     * Resolves an EXT: syntax file to an absolute web URL
     */
    protected function resolveUrlPath(string $value): string
    {
        return PathUtility::getPublicResourceWebPath($value);
    }

    /**
     * Compiles the configuration set from the outside
     * to have it easily injected into the CKEditor.
     *
     * @return array the configuration
     */
    protected function prepareConfigurationForEditor(): array
    {
        // Ensure custom config is empty so nothing additional is loaded
        // Of course this can be overridden by the editor configuration below
        $configuration = [
            'customConfig' => '',
        ];

        if ($this->data['parameterArray']['fieldConf']['config']['readOnly'] ?? false) {
            $configuration['readOnly'] = true;
        }

        $configuration['language']['content'] = $this->getLanguageIsoCodeOfContent();

        // Replace all label references
        $configuration = $this->replaceLanguageFileReferences($configuration);
        // Replace all paths
        $configuration = $this->replaceAbsolutePathsToRelativeResourcesPath($configuration);

        return $configuration;
    }

    protected function sanitizeFieldId(string $itemFormElementName): string
    {
        $fieldId = (string)preg_replace('/[^a-zA-Z0-9_:.-]/', '_', $itemFormElementName);
        return htmlspecialchars((string)preg_replace('/^[^a-zA-Z]/', 'x', $fieldId));
    }
}
