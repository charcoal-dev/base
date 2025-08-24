<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Tests\Support;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * This test class ensures that the methods in the StringHelper class
 * behave as expected when handling string manipulation tasks such as
 * cleaning spaces, trimming, and dealing with null or non-string values.
 */
class StringHelperTest extends \PHPUnit\Framework\TestCase
{
    public function testCleanSpaces_collapsesWhitespaceAndTrims(): void
    {
        $this->assertSame("foo bar baz", \Charcoal\Base\Support\Helpers\StringHelper::cleanSpaces("  foo\t\tbar \n baz  "));
        $this->assertSame("a b c", \Charcoal\Base\Support\Helpers\StringHelper::cleanSpaces("a   b\tc"));
    }

    public function testCleanSpaces_emptyAndOnlyWhitespace(): void
    {
        $this->assertSame("", \Charcoal\Base\Support\Helpers\StringHelper::cleanSpaces(""));
        $this->assertSame("", \Charcoal\Base\Support\Helpers\StringHelper::cleanSpaces(" \t\n "));
    }

    public function testGetTrimmedOrNull_returnsTrimmedWhenNonEmpty(): void
    {
        $this->assertSame("hello", \Charcoal\Base\Support\Helpers\StringHelper::getTrimmedOrNull("  hello  "));
        $this->assertSame("a b", \Charcoal\Base\Support\Helpers\StringHelper::getTrimmedOrNull(" a b "));
    }

    public function testGetTrimmedOrNull_returnsNullForWhitespaceOnlyAscii(): void
    {
        $this->assertNull(\Charcoal\Base\Support\Helpers\StringHelper::getTrimmedOrNull("     "));
        $this->assertNull(\Charcoal\Base\Support\Helpers\StringHelper::getTrimmedOrNull("\t\n\r"));
    }

    public function testGetTrimmedOrNull_nonBreakingSpaceIsNotTrimmed(): void
    {
        $nbsp = "\xC2\xA0";
        $this->assertSame($nbsp, \Charcoal\Base\Support\Helpers\StringHelper::getTrimmedOrNull($nbsp));
    }

    public static function nonStringValuesProvider(): array
    {
        return [
            [null],
            [123],
            [123.45],
            [true],
            [false],
            [[]],
            [new \stdClass()],
        ];
    }

    #[dataProvider("nonStringValuesProvider")]
    public function testGetTrimmedOrNull_returnsNullForNonStrings($value): void
    {
        $this->assertNull(\Charcoal\Base\Support\Helpers\StringHelper::getTrimmedOrNull($value));
    }
}