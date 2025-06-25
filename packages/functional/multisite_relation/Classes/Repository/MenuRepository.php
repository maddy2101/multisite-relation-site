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

namespace AbSoftlab\MultisiteRelation\Repository;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class MenuRepository
{
    // Never show or query them.
    protected $excludedDoktypes = [
        PageRepository::DOKTYPE_BE_USER_SECTION,
        PageRepository::DOKTYPE_SYSFOLDER,
    ];

    public function __construct(private readonly Context $context, private readonly PageRepository $pageRepository) {}
    public function getPageInLanguage(int $pageId, Context $context, array $configuration): array
    {
        $pageRepository = GeneralUtility::makeInstance(PageRepository::class, $context);
        $page = $pageRepository->getPage($pageId);
        /** @var LanguageAspect $languageAspect */
        $languageAspect = $context->getAspect('language');
        if (!$this->isPageIncludable($page, $configuration) || !$pageRepository->isPageSuitableForLanguage($page, $languageAspect)) {
            return [];
        }
        return $page;
    }

    protected function isPageSuitableForLanguage(array $page, LanguageAspect $languageAspect): bool
    {
        return $this->isPageIncludable($page) && $this->pageRepository->isPageSuitableForLanguage($page, $languageAspect);
    }

    protected function isPageIncludable(array $page): bool
    {
        if ($page === []) {
            return false;
        }
        return true;
    }

}
