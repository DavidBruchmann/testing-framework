<?php

declare(strict_types=1);

namespace TYPO3\TestingFramework\Tests\Unit\Core\Functional\Framework\Acceptance\Fixtures;

use TYPO3\TestingFramework\Core\Acceptance\Utility\ArrayUtility;
use TYPO3\TestingFramework\Core\Acceptance\Utility\ClassUtility;
use TYPO3\TestingFramework\Core\Acceptance\Utility\StringUtility;

class ClassWithNoComments
{
    protected const CONSTANT_ONE = 'CONSTANT_ONE';

    protected const CONSTANT_ONE_ONE = 'CONSTANT_ONE_ONE';

    protected string $propertyOne;

    protected string $propertyOneOne;

    protected string $propertyWithDefaultValue = 'DefaultValue';

    public function getPropertyOne(): string
    {
        return $this->propertyOne;
    }

    public function getPropertyOneOne(): string
    {
        return $this->propertyOneOne;
    }

    public function getPropertyWithDefaultValue(): string
    {
        return $this->propertyWithDefaultValue;
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

