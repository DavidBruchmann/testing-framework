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

use Symfony\Component\Yaml\Yaml;
use TYPO3\TestingFramework\Core\Acceptance\Utility\YamlUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class YamlUtilityTest extends UnitTestCase
{
    /**
     * @test
     * @dataProvider extractFieldsFromYamlDataProvider
     *
     * @param string $yaml
     * @param array $fields
     * @param string $expected
     */
    public function extractFieldsFromYaml(string $yaml, array $fields, string $expected): void
    {
        self::assertEquals($expected, trim(YamlHelper::extractFieldsFromYaml($yaml, $fields)));
    }

    public function extractFieldsFromYamlDataProvider(): array
    {
        $yaml = file_get_contents(__DIR__ . '/../Fixtures/Yaml.yaml');

        return [
            [
                'yaml' => $yaml,
                'fields' => ['scalar'],
                'expected' => <<<'NOWDOC'
scalar: value
NOWDOC
            ],
            [
                'yaml' => $yaml,
                'fields' => ['sequences/1'],
                'expected' => <<<'NOWDOC'
sequences:
  1:
    item1key1: value
NOWDOC
            ],
            [
                'yaml' => $yaml,
                'fields' => ['mappings/MyVendor\MyExtension\MyClass'],
                'expected' => <<<'NOWDOC'
mappings:
  MyVendor\MyExtension\MyClass:
    scalar: value
NOWDOC
            ],
            [
                'yaml' => $yaml,
                'fields' => ['mappings/MyVendor\MyExtension\MyClass', 'mappings/MyVendor\MyExtension\MyOtherClass/object/key1'],
                'expected' => <<<'NOWDOC'
mappings:
  MyVendor\MyExtension\MyClass:
    scalar: value
  MyVendor\MyExtension\MyOtherClass:
    object:
      key1: value1
NOWDOC
            ]
        ];
    }

    /**
     * @test
     */
    public function extractFieldsFromYamlIncludesFullDataIfFieldsAreEmpty(): void
    {
        $yaml = file_get_contents(__DIR__ . '/../Fixtures/Yaml.yaml');
        $fields = [];

        self::assertEquals(Yaml::parse($yaml), Yaml::parse(YamlHelper::extractFieldsFromYaml($yaml, $fields)));
    }

    /**
     * @test
     */
    public function extractFieldsFromYamlTriggersExceptionIfYamlIsEmpty(): void
    {
        $yaml = '';
        $fields = [];

        $this->expectException(\TypeError::class);

        YamlHelper::extractFieldsFromYaml($yaml, $fields);
    }
}
