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

namespace AbSoftlab\MultisiteRelation\Tests\Functional\Hreflang;

use AbSoftlab\MultisiteRelation\Tests\Functional\AbstractTest;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;

class ActionTest extends AbstractTest
{
    public function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/DatabaseInput.csv');

    }

    #[Test]
    public function companyArelatesToCompanyBRootSiteFromCompanyA()
    {
        $response = $this->executeFrontendSubRequest(new InternalRequest('https://www.company-a.com/'));
        self::assertSame(200, $response->getStatusCode());
        $stream = $response->getBody();
        $stream->rewind();
        $content = $stream->getContents();
        self::assertStringContainsString('Company A', $content);

        $expectedHreflangTags = [
            '<link rel="canonical" href="https://www.company-a.com/"/>',
            '<link rel="alternate" hreflang="en-GB" href="https://www.company-b.com/"/>',
            '<link rel="alternate" hreflang="bg-BG" href="https://www.company-b.com/bg/"/>',
            '<link rel="alternate" hreflang="hu-HU" href="https://www.company-b.com/hu/"/>',
            '<link rel="alternate" hreflang="en-US" href="https://www.company-a.com/"/>',
            '<link rel="alternate" hreflang="de-DE" href="https://www.company-a.com/de-de/"/>',
            '<link rel="alternate" hreflang="no-NO" href="https://www.company-a.com/no/"/>',
            '<link rel="alternate" hreflang="x-default" href="https://www.company-a.com/"/>',
        ];

        foreach ($expectedHreflangTags as $expectedTag) {
            self::assertStringContainsString($expectedTag, $content);
        }
    }
    #[Test]
    public function companyArelatesToCompanyBSubSiteFromCompanyA()
    {
        $response = $this->executeFrontendSubRequest(new InternalRequest('https://www.company-a.com/subpage-1'));
        self::assertSame(200, $response->getStatusCode());
        $stream = $response->getBody();
        $stream->rewind();
        $content = $stream->getContents();
        self::assertStringContainsString('Subpage 1', $content);

        $expectedHreflangTags = [
            '<link rel="canonical" href="https://www.company-a.com/subpage-1"/>',
            '<link rel="alternate" hreflang="en-GB" href="https://www.company-b.com/subpage-1"/>',
            '<link rel="alternate" hreflang="bg-BG" href="https://www.company-b.com/bg/translate-to-bulgarian-subpage-1"/>',
            '<link rel="alternate" hreflang="hu-HU" href="https://www.company-b.com/hu/translate-to-hungarian-subpage-1"/>',
            '<link rel="alternate" hreflang="en-US" href="https://www.company-a.com/subpage-1"/>',
            '<link rel="alternate" hreflang="de-DE" href="https://www.company-a.com/de-de/translate-to-german-subpage-1"/>',
            '<link rel="alternate" hreflang="no-NO" href="https://www.company-a.com/no/translate-to-norwegian-subpage-1"/>',
            '<link rel="alternate" hreflang="x-default" href="https://www.company-a.com/subpage-1"/>',
        ];

        foreach ($expectedHreflangTags as $expectedTag) {
            self::assertStringContainsString($expectedTag, $content);
        }
    }
    #[Test]
    public function companyArelatesToCompanyBSubSiteFromCompanyATranslation()
    {
        $response = $this->executeFrontendSubRequest(new InternalRequest('https://www.company-a.com/no/translate-to-norwegian-subpage-1/'));
        self::assertSame(200, $response->getStatusCode());
        $stream = $response->getBody();
        $stream->rewind();
        $content = $stream->getContents();
        self::assertStringContainsString('<title>[Translate to Norwegian:] Subpage 1</title>', $content);

        $expectedHreflangTags = [
            '<link rel="canonical" href="https://www.company-a.com/no/translate-to-norwegian-subpage-1"/>',
            '<link rel="alternate" hreflang="en-GB" href="https://www.company-b.com/subpage-1"/>',
            '<link rel="alternate" hreflang="bg-BG" href="https://www.company-b.com/bg/translate-to-bulgarian-subpage-1"/>',
            '<link rel="alternate" hreflang="hu-HU" href="https://www.company-b.com/hu/translate-to-hungarian-subpage-1"/>',
            '<link rel="alternate" hreflang="en-US" href="https://www.company-a.com/subpage-1"/>',
            '<link rel="alternate" hreflang="de-DE" href="https://www.company-a.com/de-de/translate-to-german-subpage-1"/>',
            '<link rel="alternate" hreflang="no-NO" href="https://www.company-a.com/no/translate-to-norwegian-subpage-1"/>',
            '<link rel="alternate" hreflang="x-default" href="https://www.company-a.com/subpage-1"/>',
        ];

        foreach ($expectedHreflangTags as $expectedTag) {
            self::assertStringContainsString($expectedTag, $content);
        }
    }
    #[Test]
    public function companyArelatesToCompanyBRootSiteFromCompanyB()
    {
        $response = $this->executeFrontendSubRequest(new InternalRequest('https://www.company-b.com/'));
        self::assertSame(200, $response->getStatusCode());
        $stream = $response->getBody();
        $stream->rewind();
        $content = $stream->getContents();
        self::assertStringContainsString('Company B', $content);

        $expectedHreflangTags = [
            '<link rel="canonical" href="https://www.company-b.com/"/>',
            '<link rel="alternate" hreflang="en-GB" href="https://www.company-b.com/"/>',
            '<link rel="alternate" hreflang="bg-BG" href="https://www.company-b.com/bg/"/>',
            '<link rel="alternate" hreflang="hu-HU" href="https://www.company-b.com/hu/"/>',
            '<link rel="alternate" hreflang="en-US" href="https://www.company-a.com/"/>',
            '<link rel="alternate" hreflang="de-DE" href="https://www.company-a.com/de-de/"/>',
            '<link rel="alternate" hreflang="no-NO" href="https://www.company-a.com/no/"/>',
            '<link rel="alternate" hreflang="x-default" href="https://www.company-a.com/"/>',
        ];

        foreach ($expectedHreflangTags as $expectedTag) {
            self::assertStringContainsString($expectedTag, $content);
        }
    }
}
