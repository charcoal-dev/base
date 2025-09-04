<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Tests\Registry;

use Charcoal\Base\Tests\Fixtures\StaticObjectsRegistryFixture;
use PHPUnit\Framework\TestCase;

/**
 * Test case for the StaticObjectsRegistryTrait
 */
class StaticObjectsRegistryTest extends TestCase
{
    private function flushRegistry(): void
    {
        StaticObjectsRegistryFixture::flush();
    }

    public function testGetObjectReturnsNullForNonExistentKey(): void
    {
        $this->assertNull(StaticObjectsRegistryFixture::getObject("nonExistentKey"));
    }

    public function testSetObjectStoresAndRetrievesAnObject(): void
    {
        $object = new \stdClass();
        $object->data = "someData";

        StaticObjectsRegistryFixture::setObject("ExampleKey", $object);
        $retrieved = StaticObjectsRegistryFixture::getObject("ExampleKey");
        $this->assertSame($object, $retrieved);
        $this->assertEquals("someData", $retrieved->data);
    }

    public function testHasObjectReflectsRegistryState(): void
    {
        $this->assertFalse(StaticObjectsRegistryFixture::hasObject("someKey"),
            "No object should exist yet");

        StaticObjectsRegistryFixture::setObject("someKey", new \stdClass());
        $this->assertTrue(StaticObjectsRegistryFixture::hasObject("someKey"),
            "Object should exist after set");
    }

    public function testUnsetObjectRemovesEntry(): void
    {
        $key = "tempKey";
        StaticObjectsRegistryFixture::setObject($key, new \stdClass());
        $this->assertTrue(StaticObjectsRegistryFixture::hasObject($key),
            "Object should be registered before unset");

        StaticObjectsRegistryFixture::unsetObject($key);
        $this->assertFalse(StaticObjectsRegistryFixture::hasObject($key),
            "Object should be removed after unset call");
        $this->assertNull(StaticObjectsRegistryFixture::getObject($key),
            "getObject() should return null after unsetting key");
    }

    public function testKeysAreNormalizedToLowerCase(): void
    {
        $mixedCaseKey = "SomeKeyMixedCASE";
        $object = new \stdClass();
        StaticObjectsRegistryFixture::setObject($mixedCaseKey, $object);

        $this->assertTrue(
            StaticObjectsRegistryFixture::hasObject(strtolower($mixedCaseKey)),
            "Lowercased equivalent key must exist in the registry"
        );
        $this->assertSame($object, StaticObjectsRegistryFixture::getObject(strtoupper($mixedCaseKey)));
    }

    public function testFlushRemovesAllObjects(): void
    {
        StaticObjectsRegistryFixture::setObject("keyOne", new \stdClass());
        StaticObjectsRegistryFixture::setObject("keyTwo", new \stdClass());

        // Verify objects exist prior to flush
        $this->assertTrue(StaticObjectsRegistryFixture::hasObject("keyOne"));
        $this->assertTrue(StaticObjectsRegistryFixture::hasObject("keyTwo"));

        // Flush via reflection
        $this->flushRegistry();

        // Verify registry is now empty
        $this->assertFalse(StaticObjectsRegistryFixture::hasObject("keyOne"));
        $this->assertFalse(StaticObjectsRegistryFixture::hasObject("keyTwo"));
    }
}