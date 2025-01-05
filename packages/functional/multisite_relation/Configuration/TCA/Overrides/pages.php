<?php

defined('TYPO3') or die();

$tca = [
    'columns' => [
        'multisite_relations_enable' => [
            'label' => 'LLL:EXT:multisite_relation/Resources/Private/Language/locallang_be.xlf:multisite_relations_enable.label',
            'description' => 'LLL:EXT:multisite_relation/Resources/Private/Language/locallang_be.xlf:multisite_relations_enable.description',
            'onChange' => 'reload',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        'label' => 'LLL:EXT:multisite_relation/Resources/Private/Language/locallang_be.xlf:multisite_relations_enable.check.label',
                    ],
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'multisite_relations' => [
            'label' => 'LLL:EXT:multisite_relation/Resources/Private/Language/locallang_be.xlf:multisite_relations.label',
            'description' => 'LLL:EXT:multisite_relation/Resources/Private/Language/locallang_be.xlf:multisite_relations.description',
            'displayCond' => 'FIELD:multisite_relations_enable:REQ:true',
            'config' => [
                'type' => 'group',
                'allowed' => 'pages',
                'MM' => 'tx_multisite_relation_page_page_mm',
                'suggestOptions' => [
                    'default' => [
                        'additionalSearchFields' => 'nav_title, url',
                        'addWhere' => 'AND pages.doktype = ' . \TYPO3\CMS\Core\Domain\Repository\PageRepository::DOKTYPE_DEFAULT,
                    ],
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'multisite_relations_xdefault' => [
            'label' => 'LLL:EXT:multisite_relation/Resources/Private/Language/locallang_be.xlf:multisite_relations_xdefault.label',
            'description' => 'LLL:EXT:multisite_relation/Resources/Private/Language/locallang_be.xlf:multisite_relations_xdefault.description',
            'displayCond' => [
                'AND' => [
                    'FIELD:multisite_relations_enable:REQ:true',
                    //                    'FIELD:multisite_relations:REQ:true',
                ],
            ],
            'config' => [
                //                'type' => 'select',
                //                'renderType' => 'selectCheckBox',
                'type' => 'radio',
                // items will be provided by itemsProcFunc, but some validation insists on it being present -.-
                'items' => [
                    [
                        'label' => 'foo',
                        'value' => 'bar',
                    ],
                ],
                'itemsProcFunc' => \AbSoftlab\MultisiteRelation\TCA\ItemsProcFunc\PagesXDefaultSelect::class . '->selectedRelationPages',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
    ],
];

$GLOBALS['TCA']['pages'] = array_replace_recursive($GLOBALS['TCA']['pages'], $tca);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'pages',
    '
    --div--;LLL:EXT:multisite_relation/Resources/Private/Language/locallang_be.xlf:pages.tabs.multisite_relations, 
        multisite_relations_enable, multisite_relations,multisite_relations_xdefault,
    ',
    (string)\TYPO3\CMS\Core\Domain\Repository\PageRepository::DOKTYPE_DEFAULT,
    'after:rowDescription'
);
