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

use AbSoftlab\MultisiteRelation\Event\ModifyHreflangMultisiteTagsEvent;
use AbSoftlab\MultisiteRelation\Service\RelatedPagesService;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Frontend\Event\ModifyHrefLangTagsEvent;

class HrefLangTagsListener
{
    public function __construct(
        private readonly SiteFinder $siteFinder,
        private readonly RelatedPagesService $relatedPagesService,
        private readonly EventDispatcher $eventDispatcher
    ) {}

    public function __invoke(ModifyHrefLangTagsEvent $event): void
    {
        $hreflangs = [];
        /** @var PageArguments $routing */
        $routing = $event->getRequest()->getAttribute('routing');
        $pageId = $routing->getPageId();
        $pageRecord = BackendUtility::getRecord('pages', $pageId);
        $pages = $this->relatedPagesService->getRelatedPages($pageRecord);
        foreach ($pages as $page) {
            $site = $this->siteFinder->getSiteByPageId($page['uid']);
            $language = $site->getLanguageById($page['language']['languageId']);
            $uri = $site->getRouter()->generateUri($page['uid'], ['_language' => $language]);
            $hreflangs[(string)$language->getLocale()] = (string)$uri;
        }

        $hreflangs = array_merge($hreflangs, $event->getHrefLangs());
        if ($pageRecord['multisite_relations_xdefault'] > 0) {
            $pageId = $pageRecord['multisite_relations_xdefault'];
            $site = $this->siteFinder->getSiteByPageId($pageId);
            $language = $site->getDefaultLanguage();
            $uri = $site->getRouter()->generateUri($pageId, ['_language' => $language]);
            $hreflangs['x-default'] = (string)$uri;
        }

        $hreflangs = $this->eventDispatcher->dispatch(
            new ModifyHreflangMultisiteTagsEvent($hreflangs, $language, $site)
        )->getHrefLangs();


        $event->setHrefLangs($hreflangs);
    }

}
