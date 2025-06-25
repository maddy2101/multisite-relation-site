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

namespace AbSoftlab\MultisiteRelation\Hooks;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TYPO3\CMS\Core\Database\RelationHandler;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

#[Autoconfigure(public: true)]
class DataHandlerHook
{
    public function processDatamap_beforeStart(DataHandler $pObj)
    {
        $dataMap = $pObj->datamap;
        if ($dataMap['pages']) {
            foreach ($dataMap['pages'] as $id => $fieldsArray) {
                if (!array_key_exists('multisite_relations', $fieldsArray) || $fieldsArray['multisite_relations_enable'] === '0') {
                    return;
                }

                // pick the current page as base
                $wantedRelations = [
                    $id => $id,
                ];
                $relations = GeneralUtility::trimExplode(',', $fieldsArray['multisite_relations'], true);
                foreach ($relations as $item) {
                    $uid = (int)substr($item, strpos($item, '_') + 1);
                    $wantedRelations[$uid] = $uid;
                }

                $fieldConfig = $GLOBALS['TCA']['pages']['columns']['multisite_relations'];
                $relationHandler = GeneralUtility::makeInstance(RelationHandler::class);
                $relationHandler->start(
                    'multisite_relations',
                    $fieldConfig['config']['allowed'] ?? '',
                    $fieldConfig['config']['MM'] ?? '',
                    $id,
                    'pages',
                    $fieldConfig['config'] ?? []
                );
                $relationHandler->getFromDB();
                $currentRelationsArray = $relationHandler->itemArray;
                $currentRelations = [
                    $id => $id,
                ];
                foreach ($currentRelationsArray as $item) {
                    $currentRelations[$item['id']] = $item['id'];
                }

                $unchangedRelations = array_intersect($wantedRelations, $currentRelations);

                $outputArray = [];
                foreach ($wantedRelations as $pageId) {
                    $outputArray[$pageId] = 'pages_' . $pageId;
                }

                $newRelations = array_diff($wantedRelations, $unchangedRelations);
                if (!empty($newRelations)) {
                    foreach ($newRelations as $newRelationUid) {
                        $clonedOutput = $outputArray;
                        unset($clonedOutput[$newRelationUid]);
                        $pObj->datamap['pages'][$newRelationUid]['multisite_relations_enable'] = '1';
                        $pObj->datamap['pages'][$newRelationUid]['multisite_relations'] = implode(',', $clonedOutput);
                        $pObj->datamap['pages'][$newRelationUid]['multisite_relations_xdefault'] = $dataMap['pages'][$id]['multisite_relations_xdefault'];
                    }
                }

                $removedRelations = array_diff($currentRelations, $unchangedRelations);
                foreach ($removedRelations as $removedRelationUid) {
                    $pObj->datamap['pages'][$removedRelationUid]['multisite_relations_enable'] = '0';
                    $pObj->datamap['pages'][$removedRelationUid]['multisite_relations'] = '';
                    $pObj->datamap['pages'][$removedRelationUid]['multisite_relations_xdefault'] = '0';
                }

                foreach ($currentRelations as $currentRelationUid) {
                    if (array_key_exists($currentRelationUid, $wantedRelations)) {
                        $clonedOutput = $outputArray;
                        unset($clonedOutput[$currentRelationUid]);
                        $pObj->datamap['pages'][$currentRelationUid]['multisite_relations_enable'] = '1';
                        $pObj->datamap['pages'][$currentRelationUid]['multisite_relations'] = implode(',', $clonedOutput);
                        $pObj->datamap['pages'][$currentRelationUid]['multisite_relations_xdefault'] = $dataMap['pages'][$id]['multisite_relations_xdefault'];
                    } else {
                        $pObj->datamap['pages'][$currentRelationUid]['multisite_relations_enable'] = '0';
                        $pObj->datamap['pages'][$currentRelationUid]['multisite_relations'] = '';
                        $pObj->datamap['pages'][$currentRelationUid]['multisite_relations_xdefault'] = '0';
                    }
                    if (array_key_exists($currentRelationUid, $removedRelations)) {
                        $pObj->datamap['pages'][$currentRelationUid]['multisite_relations_enable'] = '0';
                        $pObj->datamap['pages'][$currentRelationUid]['multisite_relations'] = '';
                        $pObj->datamap['pages'][$currentRelationUid]['multisite_relations_xdefault'] = '0';
                    }
                }
            }
        }
    }
}
