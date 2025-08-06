<?php
declare(strict_types=1);

/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

namespace Charcoal\Base\Tests\Registry;

use Charcoal\Base\Tests\Fixtures\Registry\SingletonChildOneFixture;
use Charcoal\Base\Tests\Fixtures\Registry\SingletonChildThreeFixture;
use Charcoal\Base\Tests\Fixtures\Registry\SingletonChildTwoFixture;
use Charcoal\Base\Tests\Fixtures\Registry\SingletonRegistryOneFixture;
use Charcoal\Base\Tests\Fixtures\Registry\SingletonRegistryTwoFixture;
use Charcoal\Base\Tests\Fixtures\Registry\StaticObjectsRegistryFixture;
use PHPUnit\Framework\TestCase;

/**
 * Demonstrates that StaticObjectsRegistry and AbstractClassSingleton
 * each have their own independent static $instances arrays, despite both
 * relying on StaticObjectsRegistryTrait.
 */
class StoreIndependenceTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        StaticObjectsRegistryFixture::flush();
        SingletonRegistryOneFixture::resetInstance();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        StaticObjectsRegistryFixture::flush();
        SingletonRegistryOneFixture::resetInstance();
    }

    public function testSingletonStoresAreSame()
    {
        StaticObjectsRegistryFixture::flush();
        SingletonRegistryOneFixture::resetInstance();

        // 1. Verify registries are empty
        $this->assertSame(0, SingletonRegistryOneFixture::count(),
            "SingletonRegistryOneFixture should be empty before we start");

        // 2. Insert object into SingletonRegistryOneFixture
        SingletonRegistryOneFixture::createInstance("registry1");
        SingletonRegistryTwoFixture::getInstance("registry1");
        SingletonChildOneFixture::createInstance("one");
        SingletonChildTwoFixture::createInstance("two");
        SingletonChildThreeFixture::getInstance("three");

        $this->assertSame(5, SingletonRegistryOneFixture::count(),
            "SingletonRegistryOneFixture should have three instances now");
        $this->assertSame(0, StaticObjectsRegistryFixture::count(),
            "StaticObjectsRegistry should still be empty");

        $this->assertTrue(SingletonRegistryOneFixture::hasInstance(SingletonRegistryTwoFixture::class),
            "Invoking hasInstance() should return true");
        $this->assertTrue(SingletonRegistryTwoFixture::hasInstance(SingletonRegistryOneFixture::class),
            "Invoking hasInstance() should return true");
        $this->assertFalse(StaticObjectsRegistryFixture::hasObject(SingletonRegistryOneFixture::class),
            "Invoking hasInstance() should return false");
        $this->assertTrue(SingletonChildOneFixture::hasInstance(SingletonChildTwoFixture::class),
            "Invoking hasInstance() should return true");
    }

    /**
     * Proves that inserting an object into StaticObjectsRegistry does not insert it into
     * the AbstractClassSingleton child, and that creating a singleton instance does not appear
     * in StaticObjectsRegistry’s internal registry.
     */
    public function testRegistriesAreIndependent(): void
    {
        StaticObjectsRegistryFixture::flush();
        SingletonRegistryOneFixture::resetInstance();

        // 1. Verify registries are empty.
        $this->assertSame(0, StaticObjectsRegistryFixture::count(),
            "StaticObjectsRegistry should be empty before we start");
        $this->assertSame(0, SingletonRegistryOneFixture::count(),
            "SingletonRegistryOneFixture should be empty before we start");

        // 2. Insert object into SingletonRegistryOneFixture
        SingletonChildOneFixture::createInstance("one");
        SingletonChildTwoFixture::createInstance("two");
        SingletonChildThreeFixture::getInstance("three");

        $this->assertSame(3, SingletonRegistryOneFixture::count(),
            "SingletonRegistryOneFixture should have three instances now");
        $this->assertSame(0, StaticObjectsRegistryFixture::count(),
            "StaticObjectsRegistry should still be empty");

        // 3. Verify that all three instances are in AbstractClassSingleton store
        $this->assertCount(3, SingletonRegistryTwoFixture::getAll(),
            "All instances should be in the store");

        // 4. Insert object into StaticObjectsRegistry
        $registryObj = new \stdClass();
        $registryObj->data = "FromRegistry";
        $registryObjId = spl_object_id($registryObj);
        StaticObjectsRegistryFixture::setObject("test_key", $registryObj);

        // 5. Verify that $registryObj cannot be found in AbstractClassSingleton store
        $this->assertArrayNotHasKey("test_key", SingletonRegistryTwoFixture::getAll());
        $this->assertArrayHasKey("test_key", StaticObjectsRegistryFixture::getAll());

        // 6. Verify that $registryObj is still in StaticObjectsRegistry
        $this->assertSame($registryObjId, spl_object_id(StaticObjectsRegistryFixture::getObject("test_key")));
    }
}
