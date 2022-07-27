<?php

declare(strict_types=1);

namespace TYPO3\TestingFramework\Tests\Unit\Core\Functional\Framework\Acceptance\Utility;

/*
 * This file is part of the TYPO3 CMS project.
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

use org\bovigo\vfs\vfsStream;
use TYPO3\TestingFramework\Core\Acceptance\Utility\FileUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class FileUtilityTest extends UnitTestCase
{
    /**
     * @test
     */
    public function getFoldersRecursively(): void
    {
        $folderTree = [
            'FolderA' => [],
            'FolderB' => [
                'SubFolderA' => []
            ],
        ];
        $expected = [
            'vfs://t3docs/FolderA',
            'vfs://t3docs/FolderB',
            'vfs://t3docs/FolderB/SubFolderA',
        ];

        $root = vfsStream::setup('t3docs', null, $folderTree);
        $actual = FileUtility::getFoldersRecursively($root->url());

        self::assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getFoldersRecursivelyHandlesMissingRootFolder(): void
    {
        $root = vfsStream::setup('t3docs');
        $actual = FileUtility::getFoldersRecursively($root->url() . '/FolderDoesNotExist');

        self::assertEquals([], $actual);
    }

    /**
     * @test
     */
    public function getSubFolders(): void
    {
        $folderTree = [
            'FolderA' => [],
            'FolderB' => [
                'SubFolderA' => []
            ],
        ];
        $expected = [
            'vfs://t3docs/FolderA',
            'vfs://t3docs/FolderB',
        ];

        $root = vfsStream::setup('t3docs', null, $folderTree);
        $actual = FileUtility::getSubFolders($root->url());

        self::assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getFilesByNameRecursively(): void
    {
        $folderTree = [
            'FolderA' => [
                'fileA.txt' => 'fileContentA'
            ],
            'FolderB' => [
                'SubFolderA' => [
                    'fileA.txt' => 'fileContentA'
                ]
            ],
            'fileA.txt' => 'fileContentA',
        ];
        $expected = [
            'vfs://t3docs/FolderA/fileA.txt',
            'vfs://t3docs/FolderB/SubFolderA/fileA.txt',
            'vfs://t3docs/fileA.txt',
        ];

        $root = vfsStream::setup('t3docs', null, $folderTree);
        $actual = FileUtility::getFilesByNameRecursively('fileA.txt', $root->url());

        self::assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function deleteRecursively(): void
    {
        $folderTree = [
            'FolderA' => [],
            'FolderB' => [
                'SubFolderA' => [
                    'fileA.txt' => 'fileContentA',
                    'fileB.txt' => 'fileContentB',
                ]
            ],
        ];

        $root = vfsStream::setup('t3docs', null, $folderTree);
        self::assertDirectoryExists($root->url());
        FileUtility::deleteRecursively($root->url());
        self::assertDirectoryDoesNotExist($root->url());
    }

    /**
     * @test
     * @dataProvider getPathBySegmentsDataProvider
     */
    public function getPathBySegments(array $segments, string $expected): void
    {
        self::assertEquals($expected, FileUtility::getPathBySegments(...$segments));
    }

    public function getPathBySegmentsDataProvider(): array
    {
        return [
            [
                'segments' => ['/absolute-path', '', 'relative-path/folder'],
                'expected' => DIRECTORY_SEPARATOR . 'absolute-path' . DIRECTORY_SEPARATOR . 'relative-path' . DIRECTORY_SEPARATOR . 'folder'
            ],
            [
                'segments' => ['relative-path/', '/', '/folder/'],
                'expected' => 'relative-path' . DIRECTORY_SEPARATOR . 'folder'
            ]
        ];
    }

    /**
     * @test
     * @dataProvider getUrlBySegmentsDataProvider
     */
    public function getUrlBySegments(array $segments, string $expected): void
    {
        self::assertEquals($expected, FileUtility::getUrlBySegments(...$segments));
    }

    public function getUrlBySegmentsDataProvider(): array
    {
        return [
            [
                'segments' => ['https://', '', 'relative-path/folder'],
                'expected' => 'https://relative-path/folder'
            ],
            [
                'segments' => ['https://absolute-path', '', 'relative-path/folder'],
                'expected' => 'https://absolute-path/relative-path/folder'
            ],
            [
                'segments' => ['/', '', 'relative-path/folder'],
                'expected' => '/relative-path/folder'
            ],
            [
                'segments' => ['/absolute-path', '', 'relative-path/folder'],
                'expected' => '/absolute-path/relative-path/folder'
            ],
            [
                'segments' => ['relative-path/', '/', '/folder/'],
                'expected' => 'relative-path/folder'
            ]
        ];
    }
}

