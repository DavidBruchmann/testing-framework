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

use TYPO3\TestingFramework\Core\Acceptance\Utility\XmlUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class XmlUtilityTest extends UnitTestCase
{
    /**
     * @test
     */
    public function extractNodesFromXmlRemovesXmlDeclaration(): void
    {
        $xml = <<<'NOWDOC'
<?xml version="1.0"?>
<T3FlexForms/>
NOWDOC;
        $expected = <<<'NOWDOC'
<T3FlexForms/>
NOWDOC;
        $xPaths = [];

        self::assertEquals($expected, trim(XmlUtility::extractNodesFromXml($xml, $xPaths)));
    }

    /**
     * @test
     */
    public function extractNodesFromXmlIncludesFullDocumentIfXPathsAreEmpty(): void
    {
        $xml = <<<'NOWDOC'
<T3FlexForms>
  <elem-1>Element 1</elem-1>
  <elem-2>Element 2</elem-2>
</T3FlexForms>
NOWDOC;
        $expected = <<<'NOWDOC'
<T3FlexForms>
  <elem-1>Element 1</elem-1>
  <elem-2>Element 2</elem-2>
</T3FlexForms>
NOWDOC;
        $xPaths = [];

        self::assertEquals($expected, trim(XmlUtility::extractNodesFromXml($xml, $xPaths)));
    }

    /**
     * @test
     */
    public function extractNodesFromXmlCanHandleMultipleXPaths(): void
    {
        $xml = <<<'NOWDOC'
<T3FlexForms>
  <elem-1>Element 1</elem-1>
  <elem-2>Element 2</elem-2>
  <elem-3>Element 3</elem-3>
</T3FlexForms>
NOWDOC;
        $expected = <<<'NOWDOC'
<T3FlexForms>
  <elem-1>Element 1</elem-1>
  <elem-2>Element 2</elem-2>
</T3FlexForms>
NOWDOC;
        $xPaths = ['/T3FlexForms/elem-1', '/T3FlexForms/elem-2'];

        self::assertEquals($expected, trim(XmlUtility::extractNodesFromXml($xml, $xPaths)));
    }

    /**
     * @test
     */
    public function extractNodesFromXmlCanHandleXPathMatchingMultipleNodes(): void
    {
        $xml = <<<'NOWDOC'
<T3FlexForms>
  <elem-1>Element 1</elem-1>
  <elem-2>Element 2</elem-2>
</T3FlexForms>
NOWDOC;
        $expected = <<<'NOWDOC'
<T3FlexForms>
  <elem-1>Element 1</elem-1>
  <elem-2>Element 2</elem-2>
</T3FlexForms>
NOWDOC;
        $xPaths = ['/T3FlexForms/*'];

        self::assertEquals($expected, trim(XmlUtility::extractNodesFromXml($xml, $xPaths)));
    }

    /**
     * @test
     */
    public function extractNodesFromXmlHandlesRelativeLikeAbsoluteXPath(): void
    {
        $xml = <<<'NOWDOC'
<T3FlexForms>
  <elem-1>Element 1</elem-1>
  <elem-2>Element 2</elem-2>
</T3FlexForms>
NOWDOC;
        $expected = <<<'NOWDOC'
<T3FlexForms>
  <elem-1>Element 1</elem-1>
</T3FlexForms>
NOWDOC;
        $relativeXPaths = ['T3FlexForms/elem-1'];
        $absoluteXPaths = ['/T3FlexForms/elem-1'];

        self::assertEquals($expected, trim(XmlUtility::extractNodesFromXml($xml, $relativeXPaths)));
        self::assertEquals($expected, trim(XmlUtility::extractNodesFromXml($xml, $absoluteXPaths)));
    }

    /**
     * @test
     */
    public function extractNodesFromXmlIncludesLastNodeOfXPathWithAllPredecessorsAndDescendants(): void
    {
        $xml = <<<'NOWDOC'
<T3FlexForms>
  <elem-1>
    <child-1>Child 1</child-1>
    <child-2>Child 2</child-2>
  </elem-1>
  <elem-2>Element 2</elem-2>
</T3FlexForms>
NOWDOC;
        $expected = <<<'NOWDOC'
<T3FlexForms>
  <elem-1>
    <child-1>Child 1</child-1>
    <child-2>Child 2</child-2>
  </elem-1>
</T3FlexForms>
NOWDOC;
        $xPaths = ['/T3FlexForms/elem-1'];

        self::assertEquals($expected, trim(XmlUtility::extractNodesFromXml($xml, $xPaths)));
    }

    /**
     * @test
     */
    public function extractNodesFromXmlIncludesAttributesOfAllNodes(): void
    {
        $xml = <<<'NOWDOC'
<T3FlexForms attribute-1="value-1">
  <elem-1 attribute-2="value-2">
    <child-1 attribute-3="value-3">Child 1</child-1>
    <child-2>Child 2</child-2>
  </elem-1>
  <elem-2>Element 2</elem-2>
</T3FlexForms>
NOWDOC;
        $expected = <<<'NOWDOC'
<T3FlexForms attribute-1="value-1">
  <elem-1 attribute-2="value-2">
    <child-1 attribute-3="value-3">Child 1</child-1>
  </elem-1>
</T3FlexForms>
NOWDOC;
        $xPaths = ['/T3FlexForms/elem-1/child-1'];

        self::assertEquals($expected, trim(XmlUtility::extractNodesFromXml($xml, $xPaths)));
    }

    /**
     * @test
     */
    public function extractNodesFromXmlTriggersErrorIfXmlIsEmpty(): void
    {
        $xml = '';
        $xPaths = ['/'];

        $this->expectError();
        $this->expectErrorMessage('DOMDocument::loadXML(): Empty string supplied as input');

        XmlUtility::extractNodesFromXml($xml, $xPaths);
    }

    /**
     * @test
     */
    public function extractNodesFromXmlThrowsExceptionOnParsingError(): void
    {
        $xml = '<broken';
        $xPaths = ['/'];

        $this->expectExceptionCode(4001);
        $this->expectExceptionMessage('XML Error #73: Couldn\'t find end of Start Tag broken line 1.');

        XmlUtility::extractNodesFromXml($xml, $xPaths);
    }

    /**
     * @test
     */
    public function extractNodesFromXmlThrowsExceptionIfXPathDoesNotMatchAnyNodes(): void
    {
        $xml = <<<'NOWDOC'
<T3FlexForms>
  <elem-1 attribute-1="value-1">text-1</elem-1>
</T3FlexForms>
NOWDOC;
        $xPaths = ['/T3FlexForms/elem-not-available'];

        $this->expectExceptionCode(4003);
        $this->expectExceptionMessage('XML Error: XPath "/T3FlexForms/elem-not-available" does not match any XML nodes.');

        XmlUtility::extractNodesFromXml($xml, $xPaths);
    }
}

