<?php

declare(strict_types=1);

namespace TYPO3\TestingFramework\Core\Tests\Unit\Functional\Framework\Acceptance\Fixtures;

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
use TYPO3\TestingFramework\Core\Acceptance\Utility\ClassUtility;
use TYPO3\TestingFramework\Core\Acceptance\Utility\StringUtility;

/**
 * The class with comments.
 */
class ClassWithComments
{
    /**
     * This is constant CONSTANT_ONE.
     */
    protected const CONSTANT_ONE = 'CONSTANT_ONE';

    /**
     * This is constant CONSTANT_ONE_ONE.
     */
    protected const CONSTANT_ONE_ONE = 'CONSTANT_ONE_ONE';

    /**
     * @var string Property One
     */
    protected string $propertyOne;

    /**
     * @var string Property One One
     */
    protected string $propertyOneOne;

    /**
     * @var string Property with default value
     */
    protected string $propertyWithDefaultValue = 'DefaultValue';

    /**
     * @return string
     */
    public function getPropertyOne(): string
    {
        return $this->propertyOne;
    }

    /**
     * @return string
     */
    public function getPropertyOneOne(): string
    {
        return $this->propertyOneOne;
    }

    /**
     * @return string
     */
    public function getPropertyWithDefaultValue(): string
    {
        return $this->propertyWithDefaultValue;
    }

    /**
     * @return array
     */
    public function extractFieldsFromArray(): array
    {
        return ArrayUtility::extractFieldsFromArray(['columns' => ['title' => 'my-title']], ['columns/title']);
    }

    /**
     * @return string
     */
    public function getMultilineTextIndented(): string
    {
        return StringUtility::indentMultilineText(':alt: This is a TCA table', '   ');
    }
}

