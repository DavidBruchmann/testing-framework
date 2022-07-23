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

use TYPO3\TestingFramework\Core\Acceptance\Utility\MathUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class MathUtilityTest extends UnitTestCase
{
    /**
     * @test
     * @dataProvider getRectangleInRectangleDataProvider
     *
     * @param int $x
     * @param int $y
     * @param int $width
     * @param int $height
     * @param int $outerWidth
     * @param int $outerHeight
     */
    public function getRectangleInRectangle(
        int $x,
        int $y,
        int $width,
        int $height,
        int $outerWidth,
        int $outerHeight,
        array $expected
    ): void {
        self::assertEquals(
            $expected,
            MathUtility::getRectangleInRectangle($x, $y, $width, $height, $outerWidth, $outerHeight)
        );
    }

    public function getRectangleInRectangleDataProvider(): array
    {
        return [
            'rectangle-fully-in-rectangle' => [
                'x' => 100,
                'y' => 50,
                'width' => 400,
                'height' => 250,
                'outerWidth' => 800,
                'outerHeight' => 600,
                'expected' => ['x' => 100, 'y' => 50, 'width' => 400, 'height' => 250]
            ],
            'rectangle-exceeds-rectangle' => [
                'x' => 100,
                'y' => 50,
                'width' => 800,
                'height' => 650,
                'outerWidth' => 800,
                'outerHeight' => 600,
                'expected' => ['x' => 100, 'y' => 50, 'width' => 700, 'height' => 550]
            ],
            'rectangle-with-distance-to-right-and-bottom-boundaries' => [
                'x' => 100,
                'y' => 50,
                'width' => -321,
                'height' => -123,
                'outerWidth' => 800,
                'outerHeight' => 600,
                'expected' => ['x' => 100, 'y' => 50, 'width' => 379, 'height' => 427]
            ],
            'rectangle-with-position-from-right-and-bottom-boundaries' => [
                'x' => -444,
                'y' => -222,
                'width' => 222,
                'height' => 111,
                'outerWidth' => 800,
                'outerHeight' => 600,
                'expected' => ['x' => 356, 'y' => 378, 'width' => 222, 'height' => 111]
            ],
            'rectangle-with-position-and-distance-from-right-and-bottom-boundaries' => [
                'x' => -444,
                'y' => -222,
                'width' => -24,
                'height' => -12,
                'outerWidth' => 800,
                'outerHeight' => 600,
                'expected' => ['x' => 356, 'y' => 378, 'width' => 420, 'height' => 210]
            ],
        ];
    }
}

