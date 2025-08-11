<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Tests\Vectors;

use Charcoal\Base\Vectors\StringTokenVector;
use PHPUnit\Framework\TestCase;

/**
 * Test case for the StringTokenVector class.
 */
final class StringTokenVectorTest extends TestCase
{
    public function testToStringWithValidGlue(): void
    {
        $tokens = new StringTokenVector(); // default: changeCase=true, uniqueTokensOnly=true
        // addTokens is protected; call via reflection
        $tokens->append('A', 'b', 'a', '  ', "\n");
        // changeCase=true => lowercased; uniqueTokensOnly=true => deduped
        $this->assertSame('a,b', $tokens->toString(','));
    }

    public function testToStringThrowsOnInvalidGlue(): void
    {
        $tokens = new StringTokenVector();
        $this->expectException(\InvalidArgumentException::class);
        $tokens->toString(', ');
    }

    public function testFilterUniqueCaseInsensitiveWhenChangeCaseFalse(): void
    {
        $tokens = new StringTokenVector(changeCase: false, uniqueTokensOnly: false);
        $tokens->append('A', 'a', 'B', 'b', 'b');
        // Explicitly de-duplicate now
        $tokens->filterUnique();
        // Should keep first occurrences with original case
        $this->assertSame(['A', 'B'], $tokens->getArray());
    }

    public function testHasTokenIsCaseInsensitive(): void
    {
        $tokens = new StringTokenVector(changeCase: false, uniqueTokensOnly: false);
        $tokens->append('Foo');

        $this->assertTrue($tokens->has('foo'));
        $this->assertTrue($tokens->has('FOO'));
        $this->assertFalse($tokens->has('bar'));
    }

    public function testDeleteTokenRemovesExactMatchWhenChangeCaseFalse(): void
    {
        $tokens = new StringTokenVector(changeCase: false, uniqueTokensOnly: false);
        $tokens->append('Foo', 'foo', 'FOO', 'Bar');

        $deleted = $tokens->delete('foo');

        $this->assertTrue($deleted);
        $this->assertSame(['Foo', 'FOO', 'Bar'], $tokens->getArray());
    }

    public function testDeleteTokenRemovesAllCaseInsensitiveMatchesWhenChangeCaseTrue(): void
    {
        $tokens = new StringTokenVector(changeCase: true, uniqueTokensOnly: false);
        $tokens->append('Foo', 'foo', 'FOO', 'Bar');

        $deleted = $tokens->delete('foo');

        $this->assertTrue($deleted);
        // With changeCase=true, tokens are normalized to lowercase and deletion is case-insensitive
        $this->assertSame(['bar'], $tokens->getArray());
    }

    public function testAppendTrimsAndSkipsEmpties(): void
    {
        $tokens = new StringTokenVector(uniqueTokensOnly: false);
        $tokens->append(' a ', '', " \t ", "b");
        $this->assertSame(['a', 'b'], $tokens->getArray());
    }

    public function testUniqueTokensOnlyAutoDedupesOnAdd(): void
    {
        $tokens = new StringTokenVector(changeCase: true, uniqueTokensOnly: true);
        $tokens->append('X', 'x', 'Y', 'y', 'Y');
        $this->assertSame(['x', 'y'], $tokens->getArray());
    }
}