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

namespace AbSoftlab\MultisiteRelation\Tests\Functional\DataManipulation;

use AbSoftlab\MultisiteRelation\Tests\Functional\AbstractTest;
use AbSoftlab\MultisiteRelation\Tests\Functional\AbstractTestCase;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;

class ActionTest extends AbstractTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/DatabaseInput.csv');
        $backendUser = $this->setUpBackendUser(1);
        $GLOBALS['LANG'] = $this->get(LanguageServiceFactory::class)->createFromUserPreferences($backendUser);

    }

    #[Test]
    public function relatedPagesReceiveData()
    {
        $input = [
            'multisite_relations_enable' => '1',
            'multisite_relations' => 'pages_4',
            'multisite_relations_xdefault' => '3',
        ];
        $this->modifyRecord('pages', 3, $input);
        $this->assertCSVDataSet(__DIR__ . '/DataSet/relatedPagesReceiveData.csv');
    }

    #[Test]
    public function removedRelationsAreSynchronizedBetweenAllPages()
    {
        // create relations to remove for the real test
        $input = [
            'multisite_relations_enable' => '1',
            'multisite_relations' => 'pages_4, pages_2',
            'multisite_relations_xdefault' => '3',
        ];
        $this->modifyRecord('pages', 3, $input);

        // now, remove one relation
        $input = [
            'multisite_relations_enable' => '1',
            'multisite_relations' => 'pages_2',
            'multisite_relations_xdefault' => '3',
        ];
        $this->modifyRecord('pages', 3, $input);
        $this->assertCSVDataSet(__DIR__ . '/DataSet/removedRelationsAreSynchronizedBetweenAllPages.csv');
    }
}
