<?php

/*
 * This file is part of ext:multisite_relation.
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

namespace AbSoftlab\MultisiteRelation\DataProcessing;

use AbSoftlab\MultisiteRelation\Service\RelatedPagesService;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

#[Autoconfigure(public: true)]
class MultisiteLanguageMenuProcessor implements DataProcessorInterface
{
    public function __construct(private readonly SiteFinder $siteFinder, private readonly RelatedPagesService $relatedPagesService) {}

    public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration, array $processedData)
    {
        if (isset($processorConfiguration['if.']) && !$cObj->checkIf($processorConfiguration['if.'])) {
            return $processedData;
        }
        $language = $cObj->getRequest()->getAttribute('language');

        $pageRecord = BackendUtility::getRecord('pages', $cObj->data['uid']);
        $pages = $this->relatedPagesService->getLocalTranslations($pageRecord);
        $pages = array_merge($pages, $this->relatedPagesService->getRelatedPages($pageRecord));
        foreach ($pages as $index => $page) {
            $pageLanguage = $this->siteFinder->getSiteByPageId($page['uid'])->getLanguageById($page['language']['languageId']);
            if ($pageLanguage === $language) {
                $pages[$index]['isActiveLanguage'] = true;
            } else {
                $pages[$index]['isActiveLanguage'] = false;
            }
        }

        $targetVariableName = $cObj->stdWrapValue('as', $processorConfiguration);
        $processedData[$targetVariableName] = $pages;
        return $processedData;
    }
}
