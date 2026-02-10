<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Tests\Support;

use Charcoal\Base\Arrays\DtoHelper;
use Charcoal\Contracts\Vectors\StringVectorInterface;
use PHPUnit\Framework\TestCase;

/**
 * Tests for DtoHelper.
 */
class DtoHelperTest extends TestCase
{
    public function testCreateFromBasicArray(): void
    {
        $input = ['a' => 1, 'b' => ['c' => 2]];
        $result = DtoHelper::createFrom($input);
        $this->assertEquals($input, $result);
    }

    public function testCreateFromBasicObject(): void
    {
        $input = new \stdClass();
        $input->a = 1;
        $input->b = 2;

        $expected = ['a' => 1, 'b' => 2];
        $result = DtoHelper::createFrom($input);
        $this->assertEquals($expected, $result);
    }

    public function testCreateFromNestedObject(): void
    {
        $child = new \stdClass();
        $child->c = 3;

        $parent = new \stdClass();
        $parent->a = 1;
        $parent->b = $child;

        $expected = ['a' => 1, 'b' => ['c' => 3]];
        $result = DtoHelper::createFrom($parent);
        $this->assertEquals($expected, $result);
    }

    public function testCreateFromWithDateTime(): void
    {
        $date = new \DateTimeImmutable('2023-01-01 12:00:00+00:00');
        $input = ['date' => $date];

        $result = DtoHelper::createFrom($input);
        $this->assertEquals(['date' => $date->format(DATE_ATOM)], $result);
    }

    public function testCreateFromWithEnums(): void
    {
        $backedEnum = TestBackedEnum::B;
        $unitEnum = TestUnitEnum::A;

        $input = [
            'backed' => $backedEnum,
            'unit' => $unitEnum
        ];

        $result = DtoHelper::createFrom($input);
        $this->assertEquals([
            'backed' => 2,
            'unit' => 'A'
        ], $result);
    }

    public function testCreateFromWithJsonSerializable(): void
    {
        $obj = new class implements \JsonSerializable {
            public function jsonSerialize(): mixed
            {
                return ['serialized' => true];
            }
        };

        $result = DtoHelper::createFrom(['obj' => $obj]);
        $this->assertEquals(['obj' => ['serialized' => true]], $result);
    }

    public function testCreateFromWithTraversable(): void
    {
        $iterator = new \ArrayIterator(['a', 'b', 'c']);
        $result = DtoHelper::createFrom($iterator);
        $this->assertEquals(['a', 'b', 'c'], $result);
    }

    public function testCreateFromWithStringVectorInterface(): void
    {
        $vector = new class implements StringVectorInterface {
            public function getArray(): array { return ['v1', 'v2']; }
            public function count(): int { return 2; }
            public function getIterator(): \Traversable { return new \ArrayIterator($this->getArray()); }
            public function join(string $glue): string { return implode($glue, $this->getArray()); }
        };

        $result = DtoHelper::createFrom($vector);
        $this->assertEquals(['v1', 'v2'], $result);
    }

    public function testCreateFromRecursionDetection(): void
    {
        $obj = new \stdClass();
        $obj->self = $obj;

        // Default onRecursion is null
        $result = DtoHelper::createFrom($obj);
        $this->assertEquals(['self' => null], $result);
    }

    public function testCreateFromWithOnRecursionCallback(): void
    {
        $obj = new \stdClass();
        $obj->name = 'recursive';
        $obj->self = $obj;

        $onRecursion = function ($object) {
            return 'RECURSION_DETECTED_' . $object->name;
        };

        $result = DtoHelper::createFrom($obj, onRecursion: $onRecursion);
        $this->assertEquals(['name' => 'recursive', 'self' => 'RECURSION_DETECTED_recursive'], $result);
    }

    public function testCreateFromWithOnRecursionString(): void
    {
        $obj = new \stdClass();
        $obj->self = $obj;

        $result = DtoHelper::createFrom($obj, onRecursion: 'LOOP');
        $this->assertEquals(['self' => 'LOOP'], $result);
    }

    public function testCreateFromMaxDepth(): void
    {
        $nested = ['level1' => ['level2' => ['level3' => 'deep']]];

        $result = DtoHelper::createFrom($nested, maxDepth: 2);
        // Level 1 is depth 0, level 2 is depth 1, level 3 would be depth 2.
        // At depth 2, it returns null.
        $this->assertEquals(['level1' => ['level2' => null]], $result);
    }

    public function testCreateFromInvalidMaxDepth(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        DtoHelper::createFrom([], 0);
    }

    public function testCreateFromNormalizeCommonShapesDisabled(): void
    {
        $date = new \DateTimeImmutable();
        $input = ['date' => $date];

        $result = DtoHelper::createFrom($input, normalizeCommonShapes: false);
        $this->assertIsArray($result['date']);
    }
}

enum TestBackedEnum: int
{
    case A = 1;
    case B = 2;
}

enum TestUnitEnum
{
    case A;
    case B;
}
