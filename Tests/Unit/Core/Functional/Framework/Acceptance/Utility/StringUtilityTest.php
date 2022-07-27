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

use TYPO3\TestingFramework\Core\Acceptance\Utility\StringUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class StringUtilityTest extends UnitTestCase
{
    /**
     * @test
     */
    public function indentMultilineText(): void
    {
        $text = <<<'NOWDOC'
:alt: This is a TCA table
:class: with-shadow

:ref:`A caption text linked to my-reference <my-reference>`
NOWDOC;
        $indentation = '   ';
        $expected = <<<'NOWDOC'
   :alt: This is a TCA table
   :class: with-shadow
   
   :ref:`A caption text linked to my-reference <my-reference>`
NOWDOC;

        self::assertEquals($expected, StringUtility::indentMultilineText($text, $indentation));
    }
}

