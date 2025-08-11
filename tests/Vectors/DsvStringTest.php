<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Tests\Vectors;

use Charcoal\Base\Support\DsvString;
use PHPUnit\Framework\TestCase;

/**
 * Test case for the DsvString class.
 */
final class DsvStringTest extends TestCase
{
    public function testDelimiterMustBeSingleByte(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new DsvString(values: null, delimiter: ', ');
    }

    public function testDelimiterMustBeSingleByteMultibyteChar(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new DsvString(values: null, delimiter: '🔥');
    }

    public function testConstructorParsesValuesTrimsSkipsEmptiesLowercasesAndDedupes(): void
    {
        $dsv = new DsvString(values: ' A, ,b,,C, a ,B ', delimiter: ','); // defaults: changeCase=true, uniqueTokensOnly=true
        $this->assertSame(['a', 'b', 'c'], $dsv->getArray());
        $this->assertSame('a,b,c', $dsv->toString());
    }

    public function testCustomDelimiterWorks(): void
    {
        $dsv = new DsvString(values: ' A;;b ; ; c ;A ', delimiter: ';');
        $this->assertSame(['a', 'b', 'c'], $dsv->getArray());
        $this->assertSame('a;b;c', $dsv->toString());
    }

    public function testChangeCaseFalsePreservesOriginalCasingButDedupesCaseInsensitively(): void
    {
        $dsv = new DsvString(values: 'Foo,foo,BAR', delimiter: ',', changeCase: false, uniqueTokensOnly: true);
        $this->assertSame(['Foo', 'BAR'], $dsv->getArray());
        $this->assertSame('Foo,BAR', $dsv->toString());
    }

    public function testAppendJoinedAddsMoreTokens(): void
    {
        $dsv = new DsvString(values: null, delimiter: ',');
        $dsv->appendJoined(' A,,b ')->appendJoined(' c ');
        $this->assertSame(['a', 'b', 'c'], $dsv->getArray());
        $this->assertSame('a,b,c', $dsv->toString());
    }

    public function testTokenCannotContainDelimiterCharacter(): void
    {
        $dsv = new DsvString(values: 'a,b', delimiter: ','); // OK
        // Force adding a single token that contains the delimiter to trigger the validation
        $this->expectException(\InvalidArgumentException::class);
        $dsv->append("x,y");
    }

    public function testFilterUniqueExplicitOnChangeCaseFalse(): void
    {
        $dsv = new DsvString(values: null, delimiter: ',', changeCase: false, uniqueTokensOnly: false);
        // Add duplicates of a differing case
        $dsv->append('A', 'a', 'B', 'b', 'B');
        $dsv->filterUnique();
        $this->assertSame(['A', 'B'], $dsv->getArray());
        $this->assertSame('A,B', $dsv->toString());
    }

    public function testToStringUsesConfiguredDelimiter(): void
    {
        $dsv = new DsvString(values: 'x|y|z|x', delimiter: '|'); // changeCase=true, uniqueTokensOnly=true
        $this->assertSame(['x', 'y', 'z'], $dsv->getArray());
        $this->assertSame('x|y|z', $dsv->toString());
    }
}