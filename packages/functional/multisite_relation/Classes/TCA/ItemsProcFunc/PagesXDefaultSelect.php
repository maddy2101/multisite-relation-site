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

namespace AbSoftlab\MultisiteRelation\TCA\ItemsProcFunc;

use TYPO3\CMS\Core\Database\RelationHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PagesXDefaultSelect
{
    public function selectedRelationPages(&$params): void
    {
        $items = [];
        $items[] = [
            'label' => '[' . $params['row']['uid'] . '] ' . $params['row']['title'] . ' (current page)',
            'value' => $params['row']['uid'],
        ];
        $selectedRelations = is_array($params['row']['multisite_relations']) ? $params['row']['multisite_relations'] : [];
        if ($selectedRelations === []) {
            $relationHandler = GeneralUtility::makeInstance(RelationHandler::class);
            $relationHandler->start('', 'pages', 'tx_multisite_relation_page_page_mm', $params['row']['uid'], 'pages');
            $selectedRelations = $relationHandler->getFromDB() ? $relationHandler->getFromDB()['pages'] : [];
        }
        foreach ($selectedRelations as $selectedRelation) {
            $items[] = [
                'label' => '[' . $selectedRelation['uid'] . '] ' . $selectedRelation['title'],
                'value' => $selectedRelation['uid'],
            ];
        }
        $params['items'] = $items;
    }

}
