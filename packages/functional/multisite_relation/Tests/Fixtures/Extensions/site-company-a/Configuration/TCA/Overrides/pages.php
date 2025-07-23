<?php

use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

(function () {
    // SAME as registered in ext_tables.php
    $customPageDoktype = 116;

    // Add the new doktype to the page type selector
    ExtensionManagementUtility::addTcaSelectItem(
        'pages',
        'doktype',
        [
            'label' => 'custom page type for company A',
            'value' => $customPageDoktype,
            'group' => 'special',
        ],
    );

    $GLOBALS['TCA']['pages']['types'][116] = $GLOBALS['TCA']['pages']['types'][PageRepository::DOKTYPE_DEFAULT];
})();
