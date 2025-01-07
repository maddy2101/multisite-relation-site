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

namespace AbSoftlab\MultisiteRelation\Tests\Functional;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class DummyTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'ab-softlab/multisite-relation',
        'b13/bolt',
        // those are registered in Build/phpunit/FunctionalTestsBootstrap.php
        'ab-softlab/site-company-a',
        'ab-softlab/site-company-b',
        'ab-softlab/site-independent-page',
    ];
    protected array $coreExtensionsToLoad = [
        'seo',
    ];

    protected array $pathsToProvideInTestInstance = [
        'typo3conf/ext/multisite_relation/Tests/Fixtures/config/sites' => 'typo3conf/sites',
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '../Fixtures/Database/BackendEnvironment.csv');

    }

    #[Test]
    public function dummy()
    {
        $response = $this->executeFrontendSubRequest(
            new InternalRequest('https://www.company-a.com/')
        );
        self::assertSame(200, $response->getStatusCode());
//        self::assertStringContainsString('',$response->getBody()->getContents());
    }
}
