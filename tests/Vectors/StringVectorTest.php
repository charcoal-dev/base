<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Tests\Vectors;

use Charcoal\Base\Vectors\StringVector;
use PHPUnit\Framework\TestCase;

/**
 * Test case for the StringVector class.
 */
final class StringVectorTest extends TestCase
{
    public function testConstructorTrimsAndSkipsEmpty(): void
    {
        $vec = new StringVector(' a ', '', '  ', "\n\tb", 'c');
        $this->assertSame(['a', 'b', 'c'], $vec->getArray());
    }

    public function testAppendTrimsSkipsAndChains(): void
    {
        $vec = new StringVector();
        $ret = $vec->append(' x ', '', "y", "  ");
        $this->assertSame($vec, $ret, 'append should be chainable');
        $this->assertSame(['x', 'y'], $vec->getArray());
    }

    public function testFilterUniqueCaseSensitive(): void
    {
        $vec = new StringVector('A', 'a', 'A', 'b', 'B', 'b');
        $vec->filterUnique();
        // Case-sensitive: 'A' and 'a' are distinct; preserves first occurrence order
        $this->assertSame(['A', 'a', 'b', 'B'], $vec->getArray());
    }

    public function testFilterUniqueIdempotent(): void
    {
        $vec = new StringVector('a', 'a', 'b');
        $vec->filterUnique();
        $this->assertSame(['a', 'b'], $vec->getArray());
        $vec->filterUnique();
        $this->assertSame(['a', 'b'], $vec->getArray(), 'Second call should not change result');
    }

    public function testWhitespaceOnlyNotAppended(): void
    {
        $vec = new StringVector(" \t ", "\n");
        $this->assertSame([], $vec->getArray());
        $vec->append("   ", "\t", "\n");
        $this->assertSame([], $vec->getArray());
    }

    public function testOrderPreservedAcrossOperations(): void
    {
        $vec = new StringVector('c', 'b', 'a', 'b', 'c', 'a');
        $vec->filterUnique();
        $this->assertSame(['c', 'b', 'a'], $vec->getArray(), 'First occurrences kept in original order');
        $vec->append('d', 'a', 'd', 'e')->filterUnique();
        $this->assertSame(['c', 'b', 'a', 'd', 'e'], $vec->getArray());
    }
}