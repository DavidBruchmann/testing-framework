<?php

declare(strict_types=1);

namespace TYPO3\TestingFramework\Core\Tests\Unit\Functional\Framework\Acceptance\Utility;

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

use TYPO3\TestingFramework\Core\Acceptance\Utility\ArrayUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ArrayUtilityTest extends UnitTestCase
{
    /**
     * @test
     * @dataProvider extractFieldsFromArrayDataProvider
     *
     * @param array $array
     * @param array $fields
     * @param array $expected
     */
    public function extractFieldsFromArray(array $array, array $fields, array $expected): void
    {
        self::assertEquals($expected, ArrayUtility::extractFieldsFromArray($array, $fields));
    }

    public function extractFieldsFromArrayDataProvider(): array
    {
        $tca = [
            'ctrl' => [],
            'columns' => [
                'title' => [
                    'exclude' => 1,
                    'label' => 'title',
                    'config' => [],
                ],
            ]
        ];

        return [
            [
                'array' => $tca,
                'fields' => ['ctrl'],
                'expected' => [
                    'ctrl' => []
                ]
            ],
            [
                'array' => $tca,
                'fields' => ['columns/title/exclude', 'columns/title/label'],
                'expected' => [
                    'columns' => [
                        'title' => [
                            'exclude' => 1,
                            'label' => 'title',
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @test
     */
    public function extractFieldsFromArrayIncludesFullDataIfFieldsAreEmpty(): void
    {
        $array = [
            'ctrl' => [],
            'columns' => [
                'title' => [
                    'label' => 'title',
                    'config' => [],
                ],
            ]
        ];
        $fields = [];

        self::assertEquals($array, ArrayUtility::extractFieldsFromArray($array, $fields));
    }
}

