<?php

defined('TYPO3') or die();

$tca = [
    'columns' => [
        'multisite_relations_enable' => [
            'label' => 'LLL:EXT:multisite_relation/Resources/Private/Language/locallang_be.xlf:multisite_relations_enable.label',
            'description' => 'LLL:EXT:multisite_relation/Resources/Private/Language/locallang_be.xlf:multisite_relations_enable.description',
            'onChange' => 'reload',
            'displayCond' => 'FIELD:sys_language_uid:=:0',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        'label' => 'LLL:EXT:multisite_relation/Resources/Private/Language/locallang_be.xlf:multisite_relations_enable.check.label',
                    ],
                ],
            ],
        ],
        'multisite_relations' => [
            'label' => 'LLL:EXT:multisite_relation/Resources/Private/Language/locallang_be.xlf:multisite_relations.label',
            'description' => 'LLL:EXT:multisite_relation/Resources/Private/Language/locallang_be.xlf:multisite_relations.description',
            'displayCond' => [
                'AND' => [
                    'FIELD:sys_language_uid:=:0',
                    'FIELD:multisite_relations_enable:REQ:true',
                ],
            ],
            'config' => [
                'type' => 'group',
                'allowed' => 'pages',
                'MM' => 'tx_multisite_relation_page_page_mm',
                'suggestOptions' => [
                    'default' => [
                        'additionalSearchFields' => 'nav_title, url',
                        'addWhere' => 'AND pages.sys_language_uid = 0 AND (pages.doktype = ' . \TYPO3\CMS\Core\Domain\Repository\PageRepository::DOKTYPE_DEFAULT . ' OR pages.doktype = ' . \TYPO3\CMS\Core\Domain\Repository\PageRepository::DOKTYPE_SHORTCUT . ')',
                    ],
                ],
            ],
        ],
        'multisite_relations_xdefault' => [
            'label' => 'LLL:EXT:multisite_relation/Resources/Private/Language/locallang_be.xlf:multisite_relations_xdefault.label',
            'description' => 'LLL:EXT:multisite_relation/Resources/Private/Language/locallang_be.xlf:multisite_relations_xdefault.description',
            'displayCond' => [
                'AND' => [
                    'FIELD:sys_language_uid:=:0',
                    'FIELD:multisite_relations_enable:REQ:true',
                ],
            ],
            'config' => [
                'type' => 'radio',
                // don't remove. It is needed for resetting the value to 0 if relation is released
                'items' => [
                    [
                        'label' => 'LLL:EXT:multisite_relation/Resources/Private/Language/locallang_be.xlf:not_set',
                        'value' => 0,
                    ],
                ],
                'itemsProcFunc' => \AbSoftlab\MultisiteRelation\TCA\ItemsProcFunc\PagesXDefaultSelect::class . '->selectedRelationPages',
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
