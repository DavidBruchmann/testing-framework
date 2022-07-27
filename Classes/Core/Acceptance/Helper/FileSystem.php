<?php

declare(strict_types=1);

namespace TYPO3\TestingFramework\Core\Acceptance\Helper;

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

use Codeception\Module;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Acceptance\Utility\FileUtility;

/**
 * Helper to access TYPO3 file system.
 */
class FileSystem extends Module
{
    public function writeFileToTypo3PublicPath(string $filePath, string $content = ''): void
    {
        $filePath = FileUtility::getPathBySegments(Environment::getPublicPath(), $filePath);
        GeneralUtility::mkdir_deep(dirname($filePath));
        GeneralUtility::writeFile($filePath, $content);
    }

    public function deleteFileInTypo3PublicPath(string $filePath): void
    {
        $filePath = FileUtility::getPathBySegments(Environment::getPublicPath(), $filePath);
        unlink($filePath);
    }
}

