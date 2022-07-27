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

use TYPO3\TestingFramework\Tests\Unit\Core\Functional\Framework\Acceptance\Fixtures\ClassWithComments;
use TYPO3\TestingFramework\Tests\Unit\Core\Functional\Framework\Acceptance\Fixtures\ClassWithNoComments;
use TYPO3\TestingFramework\Core\Acceptance\Utility\ClassUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ClassUtilityTest extends UnitTestCase
{
    /**
     * @test
     */
    public function extractMembersFromClassPrintsCodeAsIsInFile(): void
    {
        $class = ClassWithComments::class;
        $members = [
            'CONSTANT_ONE',
            'CONSTANT_ONE_ONE',
            'propertyOne',
            'propertyOneOne',
            'getPropertyOne',
            'getPropertyOneOne',
            'extractFieldsFromArray',
            'getMultilineTextIndented',
        ];
        $expected = <<<'NOWDOC'
use TYPO3\TestingFramework\Core\Acceptance\Utility\ArrayUtility;
use TYPO3\TestingFramework\Core\Acceptance\Utility\ClassUtility;
use TYPO3\TestingFramework\Core\Acceptance\Utility\StringUtility;

class ClassWithComments
{
    protected const CONSTANT_ONE = 'CONSTANT_ONE';
    protected const CONSTANT_ONE_ONE = 'CONSTANT_ONE_ONE';

    protected string $propertyOne;

    protected string $propertyOneOne;

    public function getPropertyOne(): string
    {
        return $this->propertyOne;
    }

    public function getPropertyOneOne(): string
    {
        return $this->propertyOneOne;
    }

    public function extractFieldsFromArray(): array
    {
        return ArrayUtility::extractFieldsFromArray(['columns' => ['title' => 'my-title']], ['columns/title']);
    }

    public function getMultilineTextIndented(): string
    {
        return StringUtility::indentMultilineText(':alt: This is a TCA table', '   ');
    }
}
NOWDOC;
        self::assertEquals($expected, rtrim(ClassUtility::extractMembersFromClass($class, $members)));
    }

    /**
     * @test
     */
    public function extractMembersFromClassCanIncludeComments(): void
    {
        $class = ClassWithComments::class;
        $members = [
            'CONSTANT_ONE',
            'propertyOne',
            'getPropertyOne',
        ];
        $withComment = true;
        $expected = <<<'NOWDOC'
/**
 * The class with comments.
 */
class ClassWithComments
{
    protected const CONSTANT_ONE = 'CONSTANT_ONE';

    /**
     * @var string Property One
     */
    protected string $propertyOne;

    /**
     * @return string
     */
    public function getPropertyOne(): string
    {
        return $this->propertyOne;
    }
}
NOWDOC;
        self::assertEquals($expected, rtrim(ClassUtility::extractMembersFromClass($class, $members, $withComment)));
    }

    /**
     * @test
     */
    public function extractMembersFromClassIncludesRequiredUseStatements(): void
    {
        $class = ClassWithComments::class;
        $members = [
            'extractFieldsFromArray',
            'getMultilineTextIndented'
        ];
        $expected = <<<'NOWDOC'
use TYPO3\TestingFramework\Core\Acceptance\Utility\ArrayUtility;
use TYPO3\TestingFramework\Core\Acceptance\Utility\StringUtility;

class ClassWithComments
{
    public function extractFieldsFromArray(): array
    {
        return ArrayUtility::extractFieldsFromArray(['columns' => ['title' => 'my-title']], ['columns/title']);
    }

    public function getMultilineTextIndented(): string
    {
        return StringUtility::indentMultilineText(':alt: This is a TCA table', '   ');
    }
}
NOWDOC;
        self::assertEquals($expected, rtrim(ClassUtility::extractMembersFromClass($class, $members)));
    }

    /**
     * @test
     */
    public function extractMembersFromClassThrowsReflectionExceptionIfMemberDoesNotExist(): void
    {
        $class = ClassWithComments::class;
        $members = ['member-does-not-exist'];
        $this->expectException(\ReflectionException::class);
        ClassUtility::extractMembersFromClass($class, $members);
    }

    /**
     * @test
     * @dataProvider getUseStatementsPrintsStatementAsIsInFileDataProvider
     */
    public function getUseStatementsPrintsStatementAsIsInFile(string $class, string $expected): void
    {
        self::assertEquals($expected, rtrim(ClassUtility::getUseStatements($class)));
    }

    public function getUseStatementsPrintsStatementAsIsInFileDataProvider(): array
    {
        return [
            [
                'class' => ClassWithComments::class,
                'expected' => <<<'NOWDOC'
use TYPO3\TestingFramework\Core\Acceptance\Utility\ArrayUtility;
use TYPO3\TestingFramework\Core\Acceptance\Utility\ClassUtility;
use TYPO3\TestingFramework\Core\Acceptance\Utility\StringUtility;
NOWDOC
            ],
            [
                'class' => ClassWithNoComments::class,
                'expected' => <<<'NOWDOC'
use TYPO3\TestingFramework\Core\Acceptance\Utility\ArrayUtility;
use TYPO3\TestingFramework\Core\Acceptance\Utility\ClassUtility;
use TYPO3\TestingFramework\Core\Acceptance\Utility\StringUtility;
NOWDOC
            ]
        ];
    }

    /**
     * @test
     * @dataProvider getAliasFromUseStatementDataProvider
     *
     * @param string $useStatement
     * @param string $expected
     * @return void
     */
    public function getAliasFromUseStatement(string $useStatement, string $expected): void
    {
        self::assertEquals($expected, ClassUtility::getAliasFromUseStatement($useStatement));
    }

    public function getAliasFromUseStatementDataProvider(): array
    {
        return [
            [
                'useStatement' => 'use TYPO3\TestingFramework\Core\Acceptance\Utility\ArrayUtility;',
                'expected' => 'ArrayUtility'
            ],
            [
                'useStatement' => 'use TYPO3\TestingFramework\Core\Acceptance\Utility\ArrayUtility as MyArrayUtility;',
                'expected' => 'MyArrayUtility'
            ],
            [
                'useStatement' => 'use MyArrayUtility as MyOtherArrayUtility;',
                'expected' => 'MyOtherArrayUtility'
            ]
        ];
    }

    /**
     * @test
     * @dataProvider getClassSignaturePrintsSignatureAsIsInFileDataProvider
     */
    public function getClassSignaturePrintsSignatureAsIsInFile(string $class, string $expected): void
    {
        self::assertEquals($expected, rtrim(ClassUtility::getClassSignature($class)));
    }

    public function getClassSignaturePrintsSignatureAsIsInFileDataProvider(): array
    {
        return [
            [
                'class' => ClassWithComments::class,
                'expected' => <<<'NOWDOC'
class ClassWithComments
{
%s
}
NOWDOC
            ],
            [
                'class' => ClassWithNoComments::class,
                'expected' => <<<'NOWDOC'
class ClassWithNoComments
{
%s
}
NOWDOC
            ]
        ];
    }

    /**
     * @test
     */
    public function getClassSignatureCanIncludeComment(): void
    {
        $class = ClassWithComments::class;
        $withComment = true;
        $expected = <<<'NOWDOC'
/**
 * The class with comments.
 */
class ClassWithComments
{
%s
}
NOWDOC;
        self::assertEquals($expected, rtrim(ClassUtility::getClassSignature($class, $withComment)));
    }

    /**
     * @test
     */
    public function getClassSignatureThrowsReflectionExceptionIfClassDoesNotExist(): void
    {
        $class = 'ClassDoesNotExist';
        $this->expectException(\ReflectionException::class);
        ClassUtility::getClassSignature($class);
    }

    /**
     * @test
     * @dataProvider fixDocCommentIndentationDataProvider
     */
    public function fixDocCommentIndentation(string $docComment, string $expected): void
    {
        self::assertEquals($expected, rtrim(ClassUtility::fixDocCommentIndentation($docComment)));
    }

    public function fixDocCommentIndentationDataProvider(): array
    {
        return [
            [
                'docComment' => <<<'NOWDOC'
/**
 * The class with comments.
 */
NOWDOC,
                'expected' => <<<'NOWDOC'
/**
 * The class with comments.
 */
NOWDOC
            ],
            [
                'docComment' => <<<'NOWDOC'
/**
     * @var string Property with default value
     */
NOWDOC,
                'expected' => <<<'NOWDOC'
    /**
     * @var string Property with default value
     */
NOWDOC
            ],
            [
                'docComment' => <<<'NOWDOC'
  /**
     * @return string
     */
NOWDOC,
                'expected' => <<<'NOWDOC'
    /**
     * @return string
     */
NOWDOC
            ]
        ];
    }

    /**
     * @test
     * @dataProvider getMethodCodePrintsCodeAsIsInFileDataProvider
     */
    public function getMethodCodePrintsCodeAsIsInFile(string $class, string $method, string $expected): void
    {
        self::assertEquals($expected, rtrim(ClassUtility::getMethodCode($class, $method)));
    }

    public function getMethodCodePrintsCodeAsIsInFileDataProvider(): array
    {
        return [
            [
                'class' => ClassWithComments::class,
                'method' => 'getPropertyOne',
                'expected' => <<<'NOWDOC'
    public function getPropertyOne(): string
    {
        return $this->propertyOne;
    }
NOWDOC
            ],
            [
                'class' => ClassWithNoComments::class,
                'method' => 'getPropertyOne',
                'expected' => <<<'NOWDOC'
    public function getPropertyOne(): string
    {
        return $this->propertyOne;
    }
NOWDOC
            ]
        ];
    }

    /**
     * @test
     */
    public function getMethodCodeCanIncludeComment(): void
    {
        $class = ClassWithComments::class;
        $method = 'getPropertyOne';
        $withComment = true;
        $expected = <<<'NOWDOC'
    /**
     * @return string
     */
    public function getPropertyOne(): string
    {
        return $this->propertyOne;
    }
NOWDOC;
        self::assertEquals($expected, rtrim(ClassUtility::getMethodCode($class, $method, $withComment)));
    }

    /**
     * @test
     */
    public function getMethodCodeThrowsReflectionExceptionIfMethodDoesNotExist(): void
    {
        $class = ClassWithComments::class;
        $method = 'methodDoesNotExist';
        $this->expectException(\ReflectionException::class);
        ClassUtility::getMethodCode($class, $method);
    }

    /**
     * @test
     * @dataProvider getPropertyCodePrintsCodeAsIsInFileDataProvider
     */
    public function getPropertyCodePrintsCodeAsIsInFile(string $class, string $property, string $expected): void
    {
        self::assertEquals($expected, rtrim(ClassUtility::getPropertyCode($class, $property)));
    }

    public function getPropertyCodePrintsCodeAsIsInFileDataProvider(): array
    {
        return [
            [
                'class' => ClassWithComments::class,
                'property' => 'propertyOne',
                'expected' => <<<'NOWDOC'
    protected string $propertyOne;
NOWDOC
            ],
            [
                'class' => ClassWithComments::class,
                'property' => 'propertyWithDefaultValue',
                'expected' => <<<'NOWDOC'
    protected string $propertyWithDefaultValue = 'DefaultValue';
NOWDOC
            ],
            [
                'class' => ClassWithNoComments::class,
                'property' => 'propertyOne',
                'expected' => <<<'NOWDOC'
    protected string $propertyOne;
NOWDOC
            ]
        ];
    }

    /**
     * @test
     */
    public function getPropertyCodeCanIncludeComment(): void
    {
        $class = ClassWithComments::class;
        $property = 'propertyOne';
        $withComment = true;
        $expected = <<<'NOWDOC'
    /**
     * @var string Property One
     */
    protected string $propertyOne;
NOWDOC;
        self::assertEquals($expected, rtrim(ClassUtility::getPropertyCode($class, $property, $withComment)));
    }

    /**
     * @test
     */
    public function getPropertyCodeThrowsReflectionExceptionIfPropertyDoesNotExist(): void
    {
        $class = ClassWithComments::class;
        $property = 'propertyDoesNotExist';
        $this->expectException(\ReflectionException::class);
        ClassUtility::getMethodCode($class, $property);
    }

    /**
     * @dataProvider getConstantCodePrintsCodeAsIsInFileDataProvider
     */
    public function getConstantCodePrintsCodeAsIsInFile(string $class, string $constant, string $expected): void
    {
        self::assertEquals($expected, rtrim(ClassUtility::getConstantCode($class, $constant)));
    }

    public function getConstantCodePrintsCodeAsIsInFileDataProvider(): array
    {
        return [
            [
                'class' => ClassWithComments::class,
                'constant' => 'CONSTANT_ONE',
                'expected' => <<<'NOWDOC'
    protected const CONSTANT_ONE = 'CONSTANT_ONE';
NOWDOC
            ],
            [
                'class' => ClassWithNoComments::class,
                'constant' => 'CONSTANT_ONE',
                'expected' => <<<'NOWDOC'
    protected const CONSTANT_ONE = 'CONSTANT_ONE';
NOWDOC
            ]
        ];
    }

    /**
     * @test
     */
    public function getConstantCodeThrowsReflectionExceptionIfConstantDoesNotExist(): void
    {
        $class = ClassWithComments::class;
        $constant = 'CONSTANT_DOES_NOT_EXIST';
        $this->expectException(\ReflectionException::class);
        ClassUtility::getConstantCode($class, $constant);
    }
}

