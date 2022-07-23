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

use TYPO3\TestingFramework\Core\Acceptance\Utility\JsonUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class JsonUtilityTest extends UnitTestCase
{
    /**
     * @test
     * @dataProvider extractFieldsFromJsonDataProvider
     *
     * @param string $json
     * @param array $fields
     * @param string $expected
     */
    public function extractFieldsFromJson(string $json, array $fields, string $expected): void
    {
        self::assertEquals($expected, trim(JsonUtility::extractFieldsFromJson($json, $fields)));
    }

    public function extractFieldsFromJsonDataProvider(): array
    {
        $json = file_get_contents(__DIR__ . '/../Fixtures/Json.json');

        return [
            [
                'json' => $json,
                'fields' => ['string'],
                'expected' => <<<'NOWDOC'
{
    "string": "value"
}
NOWDOC
            ],
            [
                'json' => $json,
                'fields' => ['array/1'],
                'expected' => <<<'NOWDOC'
{
    "array": {
        "1": 2
    }
}
NOWDOC
            ],
            [
                'json' => $json,
                'fields' => ['object/string'],
                'expected' => <<<'NOWDOC'
{
    "object": {
        "string": "value-3"
    }
}
NOWDOC
            ],
            [
                'json' => $json,
                'fields' => ['string', 'object'],
                'expected' => <<<'NOWDOC'
{
    "string": "value",
    "object": {
        "string": "value-3",
        "number": 3,
        "array": [
            "value-3-1",
            3.1000000000000001,
            {
                "string": "value-3-2-1: value-3-2-2, value-3-2-3",
                "number": 3.2000000000000002
            },
            "value-3-3"
        ]
    }
}
NOWDOC
            ]
        ];
    }

    /**
     * @test
     */
    public function extractFieldsFromJsonCombinesPrettyJsonAndInlineJson(): void
    {
        $json = file_get_contents(__DIR__ . '/../Fixtures/Json.json');
        $fields = ['object'];
        $expected = <<<'NOWDOC'
{
    "object": {
        "string": "value-3",
        "number": 3,
        "array": ["value-3-1", 3.1000000000000001, {"string": "value-3-2-1: value-3-2-2, value-3-2-3", "number": 3.2000000000000002}, "value-3-3"]
    }
}
NOWDOC;

        self::assertEquals($expected, JsonUtility::extractFieldsFromJson($json, $fields, 2));
    }

    /**
     * @test
     */
    public function extractFieldsFromJsonIncludesFullDataIfFieldsAreEmpty(): void
    {
        $json = file_get_contents(__DIR__ . '/../Fixtures/Json.json');
        $fields = [];

        self::assertEquals(json_decode($json), json_decode(JsonUtility::extractFieldsFromJson($json, $fields)));
    }

    /**
     * @test
     */
    public function extractFieldsFromJsonTriggersExceptionIfJsonIsEmpty(): void
    {
        $json = '';
        $fields = [];

        $this->expectException(\JsonException::class);

        JsonUtility::extractFieldsFromJson($json, $fields);
    }

    /**
     * @test
     */
    public function extractFieldsFromJsonTriggersExceptionIfJsonIsMalformed(): void
    {
        $json = <<<'NOWDOC'
{
    "key": "value",
}
NOWDOC;
        $fields = [];

        $this->expectException(\JsonException::class);

        JsonUtility::extractFieldsFromJson($json, $fields);
    }
}

