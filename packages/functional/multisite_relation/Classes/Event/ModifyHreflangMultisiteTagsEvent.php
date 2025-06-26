<?php

namespace AbSoftlab\MultisiteRelation\Event;

use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

class ModifyHreflangMultisiteTagsEvent
{
    public function __construct(
        private array $hrefLangs,
        private SiteLanguage $siteLanguage,
        private Site $site,
    ) {}

    public function getHrefLangs(): array
    {
        return $this->hrefLangs;
    }

    /**
     * Set the hreflangs. This should be an array in format:
     *  [
     *     'en-US' => 'https://example.com',
     *     'nl-NL' => 'https://example.com/nl'
     *  ]
     */
    public function setHrefLangs(array $hrefLangs): void
    {
        $this->hrefLangs = $hrefLangs;
    }

    public function getSiteLanguage(): array
    {
        return $this->siteLanguage->toArray();
    }

    public function getSite(): Site
    {
        return $this->site;
    }
}
