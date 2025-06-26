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

namespace AbSoftlab\MultisiteRelation\Service;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Database\RelationHandler;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RelatedPagesService
{
    public function __construct(private readonly SiteFinder $siteFinder) {}

    public function getRelatedPages(array $pageRecord): array
    {
        if ($pageRecord['multisite_relations_enable'] !== 1) {
            return [];
        }
        $pages = [];
        if ($pageRecord['multisite_relations'] > 0) {
            $fieldConfig = $GLOBALS['TCA']['pages']['columns']['multisite_relations'];
            $relationHandler = GeneralUtility::makeInstance(RelationHandler::class);
            $relationHandler->start(
                'multisite_relations',
                $fieldConfig['config']['allowed'] ?? '',
                $fieldConfig['config']['MM'] ?? '',
                $pageRecord['uid'],
                'pages',
                $fieldConfig['config'] ?? []
            );
            $selectedRelations = $relationHandler->getFromDB()['pages'] ?? [];
            if (!empty($selectedRelations)) {
                foreach ($selectedRelations as $selectedRelation) {
                    $site = $this->siteFinder->getSiteByPageId($selectedRelation['uid']);
                    foreach ($site->getLanguages() as $language) {
                        $pageRepository = $this->buildPageRepository(new LanguageAspect($language->getLanguageId()));
                        $page = $pageRepository->getPage($selectedRelation['uid']);
                        if (empty($page)) {
                            continue;
                        }
                        $page['language'] = $language->toArray();
                        // if translation is not available (e.g. hidden), the original record is returned.
                        // skip further processing then
                        if ($language->getLanguageId() > 0 && !isset($page['_PAGES_OVERLAY'])) {
                            continue;
                        }
                        $pages[] = $page;
                    }

                }
            }
        }
        return $pages;
    }

    public function getLocalTranslations(array $pageRecord)
    {
        $pages = [];
        $site = $this->siteFinder->getSiteByPageId($pageRecord['uid']);
        foreach ($site->getLanguages() as $language) {
            $pageRepository = $this->buildPageRepository(new LanguageAspect($language->getLanguageId()));
            $page = $pageRepository->getPage($pageRecord['uid']);
            $page['language'] = $language->toArray();
            // if translation is not available (e.g. hidden), the original record is returned.
            // skip further processing then
            if ($language->getLanguageId() > 0 && !isset($page['_PAGES_OVERLAY'])) {
                continue;
            }
            $pages[] = $page;
        }
        return $pages;
    }

    /**
     * Builds PageRepository instance without depending on global context, e.g.
     * not automatically overlaying records based on current request language.
     */
    protected function buildPageRepository(?LanguageAspect $languageAspect = null): PageRepository
    {
        // clone global context object (singleton)
        $context = clone GeneralUtility::makeInstance(Context::class);
        $context->setAspect(
            'language',
            $languageAspect ?? GeneralUtility::makeInstance(LanguageAspect::class)
        );
        return GeneralUtility::makeInstance(
            PageRepository::class,
            $context
        );
    }
}
