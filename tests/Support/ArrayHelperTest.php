<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Tests\Support;

use Charcoal\Base\Arrays\ArrayHelper;

/**
 * Tests for ArrayHelper.
 */
class ArrayHelperTest extends \PHPUnit\Framework\TestCase
{
    public function testIsSequentialWithEmptyArray(): void
    {
        $this->assertFalse(ArrayHelper::isSequential([]));
    }

    public function testIsSequentialWithSequentialArray(): void
    {
        $this->assertTrue(ArrayHelper::isSequential([1, 2, 3]));
        $this->assertTrue(ArrayHelper::isSequential(["a", "b", "c"]));
        $this->assertTrue(ArrayHelper::isSequential([0 => "first", 1 => "second", 2 => "third"]));
    }

    public function testIsSequentialWithAssociativeArray(): void
    {
        $this->assertFalse(ArrayHelper::isSequential(["key" => "value"]));
        $this->assertFalse(ArrayHelper::isSequential(["a" => 1, "b" => 2]));
        $this->assertFalse(ArrayHelper::isSequential([1 => "first", 0 => "second"]));
    }

    public function testIsSequentialWithNonSequentialNumericKeys(): void
    {
        $this->assertFalse(ArrayHelper::isSequential([0 => "a", 2 => "b"]));
        $this->assertFalse(ArrayHelper::isSequential([1 => "a", 2 => "b"]));
    }

    public function testMergeAssocDeepBasicMerge(): void
    {
        $a = ["key1" => "value1", "key2" => "value2"];
        $b = ["key2" => "newValue2", "key3" => "value3"];

        $result = ArrayHelper::mergeAssocDeep($a, $b);

        $expected = ["key1" => "value1", "key2" => "newValue2", "key3" => "value3"];
        $this->assertEquals($expected, $result);
    }

    public function testMergeAssocDeepWithEmptyArrays(): void
    {
        $this->assertEquals([], ArrayHelper::mergeAssocDeep([], []));
        $this->assertEquals(["a" => 1], ArrayHelper::mergeAssocDeep([], ["a" => 1]));
        $this->assertEquals(["a" => 1], ArrayHelper::mergeAssocDeep(["a" => 1], []));
    }

    public function testMergeAssocDeepNestedAssociativeArrays(): void
    {
        $a = [
            "config" => [
                "database" => ["host" => "localhost", "port" => 3306],
                "cache" => ["driver" => "redis"]
            ]
        ];

        $b = [
            "config" => [
                "database" => ["port" => 5432, "name" => "mydb"],
                "logging" => ["level" => "debug"]
            ]
        ];

        $result = ArrayHelper::mergeAssocDeep($a, $b);

        $expected = [
            "config" => [
                "database" => ["host" => "localhost", "port" => 5432, "name" => "mydb"],
                "cache" => ["driver" => "redis"],
                "logging" => ["level" => "debug"]
            ]
        ];

        $this->assertEquals($expected, $result);
    }

    public function testMergeAssocDeepWithSequentialArrays(): void
    {
        $a = ["items" => [1, 2, 3]];
        $b = ["items" => [4, 5, 6]];

        $result = ArrayHelper::mergeAssocDeep($a, $b);

        // Sequential arrays should be replaced, not merged
        $expected = ["items" => [4, 5, 6]];
        $this->assertEquals($expected, $result);
    }

    public function testMergeAssocDeepMixedArrayTypes(): void
    {
        $a = [
            "assoc" => ["key1" => "value1"],
            "list" => [1, 2, 3],
            "scalar" => "old"
        ];

        $b = [
            "assoc" => ["key2" => "value2"],
            "list" => [4, 5],
            "scalar" => "new"
        ];

        $result = ArrayHelper::mergeAssocDeep($a, $b);

        $expected = [
            "assoc" => ["key1" => "value1", "key2" => "value2"],
            "list" => [4, 5],
            "scalar" => "new"
        ];

        $this->assertEquals($expected, $result);
    }

    public function testMergeAssocDeepDeeplyNested(): void
    {
        $a = [
            "level1" => [
                "level2" => [
                    "level3" => ["deep" => "original"]
                ]
            ]
        ];

        $b = [
            "level1" => [
                "level2" => [
                    "level3" => ["deeper" => "added"]
                ]
            ]
        ];

        $result = ArrayHelper::mergeAssocDeep($a, $b);

        $expected = [
            "level1" => [
                "level2" => [
                    "level3" => ["deep" => "original", "deeper" => "added"]
                ]
            ]
        ];

        $this->assertEquals($expected, $result);
    }

    public function testMergeAssocDeepArrayOverwritesScalar(): void
    {
        $a = ["key" => "scalar"];
        $b = ["key" => ["array" => "value"]];

        $result = ArrayHelper::mergeAssocDeep($a, $b);

        $expected = ["key" => ["array" => "value"]];
        $this->assertEquals($expected, $result);
    }

    public function testMergeAssocDeepScalarOverwritesArray(): void
    {
        $a = ["key" => ["array" => "value"]];
        $b = ["key" => "scalar"];

        $result = ArrayHelper::mergeAssocDeep($a, $b);

        $expected = ["key" => "scalar"];
        $this->assertEquals($expected, $result);
    }

    public function testCheckDepthWithEmptyArray(): void
    {
        $this->assertEquals(0, ArrayHelper::checkDepth([]));
    }

    public function testCheckDepthWithFlatArray(): void
    {
        $array = ["a", "b", "c"];
        $this->assertEquals(0, ArrayHelper::checkDepth($array));
    }

    public function testCheckDepthWithNestedArrays(): void
    {
        $array = ["level1" => ["level2" => ["level3" => "value"]]];
        $this->assertEquals(2, ArrayHelper::checkDepth($array));
    }

    public function testCheckDepthWithLimit(): void
    {
        $array = ["level1" => ["level2" => ["level3" => "value"]]];
        $this->assertEquals(2, ArrayHelper::checkDepth($array, 2));
    }

    public function testCheckDepthWithInvalidLimit(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid depth limit to crosscheck: -1");

        ArrayHelper::checkDepth([], -1);
    }

    public function testCheckDepthWithObject(): void
    {
        $array = ["object" => new \stdClass()];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Object detected at depth: 0");

        ArrayHelper::checkDepth($array);
    }

    public function testCheckDepthWithNestedObject(): void
    {
        $array = ["level1" => ["level2" => new \stdClass()]];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Object detected at depth: 1");

        ArrayHelper::checkDepth($array);
    }

    public function testCanonicalizeLexicographicWithSequentialArray(): void
    {
        $array = ["c", "a", "b"];
        $result = ArrayHelper::canonicalizeLexicographic($array);

        // Sequential arrays should remain unchanged
        $this->assertEquals(["c", "a", "b"], $result);
    }

    public function testCanonicalizeLexicographicWithAssociativeArray(): void
    {
        $array = ["z" => 1, "a" => 2, "m" => 3];
        $result = ArrayHelper::canonicalizeLexicographic($array);

        $expected = ["a" => 2, "m" => 3, "z" => 1];
        $this->assertEquals($expected, $result);
    }

    public function testCanonicalizeLexicographicWithNumericKeys(): void
    {
        $array = ["10" => "ten", "2" => "two", "1" => "one"];
        $result = ArrayHelper::canonicalizeLexicographic($array);

        $expected = ["1" => "one", "2" => "two", "10" => "ten"];
        $this->assertEquals($expected, $result);
    }

    public function testCanonicalizeLexicographicWithMixedKeys(): void
    {
        $array = ["z" => 1, "10" => "ten", "a" => 2, "2" => "two"];
        $result = ArrayHelper::canonicalizeLexicographic($array);

        $expected = ["2" => "two", "10" => "ten", "a" => 2, "z" => 1];
        $this->assertEquals($expected, $result);
    }

    public function testCanonicalizeLexicographicRecursive(): void
    {
        $array = [
            "z" => ["c" => 1, "a" => 2],
            "a" => [3, 2, 1],
            "m" => ["z" => ["b" => 1, "a" => 2]]
        ];

        $result = ArrayHelper::canonicalizeLexicographic($array);

        $expected = [
            "a" => [3, 2, 1], // Sequential array remains unchanged
            "m" => ["z" => ["a" => 2, "b" => 1]],
            "z" => ["a" => 2, "c" => 1]
        ];

        $this->assertEquals($expected, $result);
    }

    public function testCanonicalizeLexicographicWithEmptyArray(): void
    {
        $this->assertEquals([], ArrayHelper::canonicalizeLexicographic([]));
    }
}