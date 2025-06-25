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

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

abstract class AbstractTestCase extends FunctionalTestCase
{
    protected ?DataHandler $dataHandler = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/Database/be_user_setup.csv');

    }

    protected array $pathsToLinkInTestInstance = [
        'typo3conf/ext/multisite_relation/Tests/Fixtures/config/sites' => 'typo3conf/sites',
    ];
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
        'fluid_styled_content',
    ];

    /**
     * Modify an existing record.
     *
     * Example:
     * modifyRecord('tt_content', 42, ['hidden' => '1']); // Modify a single record
     * modifyRecord('tt_content', 42, ['hidden' => '1'], ['tx_irre_table' => [4]]); // Modify a record and delete a child
     */
    public function modifyRecord(string $tableName, int $uid, array $recordData, ?array $deleteTableRecordIds = null): void
    {
        $dataMap = [
            $tableName => [
                $uid => $recordData,
            ],
        ];
        $commandMap = [];
        if (!empty($deleteTableRecordIds)) {
            foreach ($deleteTableRecordIds as $tableName => $recordIds) {
                foreach ($recordIds as $recordId) {
                    $commandMap[$tableName][$recordId]['delete'] = true;
                }
            }
        }
        $this->createDataHandler();
        $this->dataHandler->start($dataMap, $commandMap);
        $this->dataHandler->process_datamap();
        if (!empty($commandMap)) {
            $this->dataHandler->process_cmdmap();
        }
    }

    protected function createDataHandler(): DataHandler
    {
        $this->dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $backendUser = $this->getBackendUser();
        if (isset($backendUser->uc['copyLevels'])) {
            $this->dataHandler->copyTree = $backendUser->uc['copyLevels'];
        }
        return $this->dataHandler;
    }

    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
