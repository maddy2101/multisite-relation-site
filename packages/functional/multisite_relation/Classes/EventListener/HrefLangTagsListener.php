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

namespace AbSoftlab\MultisiteRelation\EventListener;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Database\RelationHandler;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Event\ModifyHrefLangTagsEvent;

class HrefLangTagsListener
{
    public function __construct(
        private readonly RelationHandler $relationHandler,
        private readonly SiteFinder $siteFinder,
    ) {}

    public function __invoke(ModifyHrefLangTagsEvent $event): void
    {
        $hreflangs = [];
        /** @var PageArguments $routing */
        $routing = $event->getRequest()->getAttribute('routing');
        /** @var SiteLanguage $language */
        $language = $event->getRequest()->getAttribute('language');
        $pageId = $routing->getPageId();
        $languageId = $language->getLanguageId();
        $pageRepository = $this->buildPageRepository(new LanguageAspect($languageId));
        $pageRecord = $pageRepository->getPage($pageId);
        if ($pageRecord['multisite_relations_enable'] !== 1) {
            return;
        }
        if ($pageRecord['multisite_relations'] > 0) {
            $this->relationHandler->start('', 'pages', 'tx_multisite_relation_page_page_mm', $pageId, 'pages');
            $selectedRelations = $this->relationHandler->getFromDB() ? $this->relationHandler->getFromDB()['pages'] : [];
            if (!empty($selectedRelations)) {
                foreach ($selectedRelations as $selectedRelation) {
                    $site = $this->siteFinder->getSiteByPageId($selectedRelation['uid']);
                    foreach ($site->getLanguages() as $language) {
                        $pageRepository = $this->buildPageRepository(new LanguageAspect($language->getLanguageId()));
                        $page = $pageRepository->getPage($selectedRelation['uid']);
                        // if translation is not available (e.g. hidden), the original record is returned.
                        // skip further processing then
                        if ($language->getLanguageId() > 0 && !isset($page['_PAGES_OVERLAY'])) {
                            continue;
                        }
                        $uri = $site->getRouter()->generateUri($page['uid'], ['_language' => $language]);
                        $hreflangs[(string)$language->getLocale()] = (string)$uri;
                    }

                }
            }
        }
        $hreflangs = array_merge($hreflangs, $event->getHrefLangs());
        if ($pageRecord['multisite_relations_xdefault'] > 0) {
            $pageId = $pageRecord['multisite_relations_xdefault'];
            $site = $this->siteFinder->getSiteByPageId($pageId);
            $language = $site->getDefaultLanguage();
            $uri = $site->getRouter()->generateUri($pageId, ['_language' => $language]);
            $hreflangs['x-default'] = (string)$uri;
        }

        $event->setHrefLangs($hreflangs);
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
