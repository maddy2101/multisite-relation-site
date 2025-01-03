<?php

declare(strict_types=1);

/*
 * This file is part of TYPO3 CMS-based extension "hreflang_multisite" by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace AbSoftlab\MultisiteRelation\Tests\Acceptance\Support\Extension;

use Codeception\Event\SuiteEvent;
use Symfony\Component\Mailer\Transport\NullTransport;
use TYPO3\TestingFramework\Composer\ComposerPackageManager;
use TYPO3\TestingFramework\Core\Acceptance\Extension\BackendEnvironment as BaseBackendEnvironment;
use TYPO3\TestingFramework\Core\Testbase;

/**
 * Load various core extensions and styleguide and call styleguide generator
 */
class BackendEnvironment extends BaseBackendEnvironment
{
    /**
     * Load a list of core extensions and styleguide
     *
     * @var array
     */
    protected $localConfig = [
        'coreExtensionsToLoad' => [
            'core',
            'frontend',
            'recordlist',
            'backend',
            'seo',
        ],
        'testExtensionsToLoad' => [
            'ab-softlab/multisite-relation',
        ],
        'csvDatabaseFixtures' => [
            __DIR__ . '/../../Fixtures/BackendEnvironment.csv',
        ],
        'configurationToUseInTestInstance' => [
            'MAIL' => [
                'transport' => NullTransport::class,
            ],
            'SYS' => [
                'features' => [
                    'security.backend.enforceContentSecurityPolicy' => true,
                ],
            ],
        ],
        'additionalFoldersToCreate' => [
            '/fileadmin/user_upload/',
            '/typo3temp/var/lock',
        ],
    ];

    public function bootstrapTypo3Environment(SuiteEvent $suiteEvent) {
        parent::bootstrapTypo3Environment($suiteEvent);

        $testbase = new Testbase();
        $composerPackageManager = new ComposerPackageManager();
        $projectRoot = $composerPackageManager->getRootPath();

        // provide fixture site configuration
        @symlink($projectRoot . '/Tests/Acceptance/Fixtures/sites', $projectRoot. '/.Build/Web/typo3conf/sites');

        $copyFiles = [
            [
                'extension' => 'backend',
                'path' => 'Resources/Public/Icons/favicon.ico',
                'target' => 'favicon.ico',
            ],
            [
                'extension' => 'install',
                'path' => 'Resources/Private/FolderStructureTemplateFiles/root-htaccess',
                'target' => '.htaccess',
            ],
            [
                'extension' => 'install',
                'path' => 'Resources/Private/FolderStructureTemplateFiles/resources-root-htaccess',
                'target' => 'fileadmin/.htaccess',
            ],
            [
                'extension' => 'install',
                'path' => 'Resources/Private/FolderStructureTemplateFiles/resources-root-htaccess',
                'target' => 'fileadmin/_temp_/.htaccess',
            ],
            [
                'extension' => 'install',
                'path' => 'Resources/Private/FolderStructureTemplateFiles/fileadmin-temp-index.html',
                'target' => 'fileadmin/_temp_/index.html',
            ],
            [
                'extension' => 'install',
                'path' => 'Resources/Private/FolderStructureTemplateFiles/typo3temp-var-htaccess',
                'target' => 'typo3temp/var/.htaccess',
            ],
        ];
        foreach ($copyFiles as $copyFile) {
            $extensionKey = $copyFile['extension'];
            $path = $copyFile['path'];
            $targetFile = $copyFile['target'];
            $packageInfo = $composerPackageManager->getPackageInfo($extensionKey);
            if ($packageInfo === null) {
                throw new \RuntimeException(
                    sprintf('Could not get package information for "%s"', 'backend'),
                    1729599626,
                );
            }
            $sourceFile = rtrim($packageInfo->getPath(), '/') . '/' . ltrim($path, '/');
            $targetPath = dirname(ltrim($targetFile, '/'));
            if ($targetPath !== '' && $targetPath !== '.' && $targetPath !== '') {
                $testbase->createDirectory($targetPath);
            }
            copy(
                from: $sourceFile,
                to: ltrim($targetFile, '/'),
            );
        }
    }
}
