<?php
declare(strict_types=1);

/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

namespace Charcoal\Base\Tests\Traits;

use Charcoal\Base\Objects\Traits\NoDumpTrait;
use Charcoal\Base\Tests\Fixtures\CompositeTraitsFixture;
use PHPUnit\Framework\TestCase;

class CompositeTraitsTest extends TestCase
{
    public function testCannotClone()
    {
        $obj = new CompositeTraitsFixture("Alpha", 3);
        $this->expectException(\BadMethodCallException::class);
        /** @noinspection PhpExpressionResultUnusedInspection */
        clone $obj;
    }

    public function testCannotSerialize()
    {
        $obj = new CompositeTraitsFixture("Beta");
        $this->expectException(\BadMethodCallException::class);
        serialize($obj);
    }

    public function testNoDumpTrait()
    {
        $obj = new CompositeTraitsFixture("Gamma", 5);
        $debug = $obj->__debugInfo();
        $this->assertIsArray($debug);
        $this->assertContains(CompositeTraitsFixture::class, $debug);
    }

    public function testProperties()
    {
        $obj = new CompositeTraitsFixture("Delta", 7);
        $this->assertSame("Delta", $obj->name);
        $this->assertSame(7, $obj->level);
        $this->assertInstanceOf(\DateTimeImmutable::class, $obj->getCreatedAt());
    }

    public function testDebugInfoReturnsClassNameArray(): void
    {
        $obj = new class {
            use NoDumpTrait;
        };

        $result = $obj->__debugInfo();
        $expected = [get_class($obj), spl_object_id($obj)];

        $this->assertSame($expected, $result);
    }
}