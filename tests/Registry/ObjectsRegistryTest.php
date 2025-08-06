<?php
declare(strict_types=1);

/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

namespace Charcoal\Base\Tests\Registry;

use Charcoal\Base\Tests\Fixtures\Registry\ObjectsRegistryFixture;
use PHPUnit\Framework\TestCase;

class ObjectsRegistryTest extends TestCase
{
    /**
     * @var ObjectsRegistryFixture
     */
    private ObjectsRegistryFixture $fixture;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fixture = new ObjectsRegistryFixture();
    }

    public function testGetObjectReturnsNullForNonExistentKey(): void
    {
        $this->assertNull(
            $this->fixture->getObject("nonExistentKey"),
            "Should return null when object does not exist"
        );
    }

    public function testSetObjectStoresAndRetrievesAnObject(): void
    {
        $key = "exampleKey";
        $object = new \stdClass();
        $object->data = "someData";

        $this->fixture->setObject($key, $object);

        $retrieved = $this->fixture->getObject($key);
        $this->assertNotNull($retrieved, "Should retrieve an object after it is set");
        $this->assertSame($object, $retrieved, "Object retrieved should be the same instance");
        $this->assertSame("someData", $retrieved->data, "Object should retain the data property");
    }

    public function testHasObjectReflectsRegistryState(): void
    {
        $key = "someKey";
        $this->assertFalse(
            $this->fixture->hasObject($key),
            "No object should exist initially under this key"
        );

        $this->fixture->setObject($key, new \stdClass());
        $this->assertTrue(
            $this->fixture->hasObject($key),
            "Object should exist after setting it"
        );
    }

    public function testUnsetObjectRemovesEntry(): void
    {
        $key = "tempKey";
        $this->fixture->setObject($key, new \stdClass());

        $this->assertTrue(
            $this->fixture->hasObject($key),
            "Object should be registered before unset"
        );

        $this->fixture->unsetObject($key);

        $this->assertFalse(
            $this->fixture->hasObject($key),
            "Object should be removed after calling unsetObject"
        );
        $this->assertNull(
            $this->fixture->getObject($key),
            "Once removed, getObject() should return null"
        );
    }

    public function testKeysAreNormalizedToLowerCase(): void
    {
        $mixedCaseKey = "MixedCaseKey";
        $object = new \stdClass();

        $this->fixture->setObject($mixedCaseKey, $object);
        // Check existence using lowercase
        $this->assertTrue(
            $this->fixture->hasObject(strtolower($mixedCaseKey)),
            "Lowercase version should exist after storing the mixed-case key"
        );

        // Even with uppercase retrieval, should get the same object
        $retrieved = $this->fixture->getObject(strtoupper($mixedCaseKey));
        $this->assertSame($object, $retrieved, "Registry should be case-insensitive for stored keys");
    }

    public function testDistinctStorageAcrossInstances(): void
    {
        $firstRegistry = new ObjectsRegistryFixture();
        $secondRegistry = new ObjectsRegistryFixture();

        $key = "uniqueKey";
        $firstObject = new \stdClass();
        $firstObject->value = "first";

        $firstRegistry->setObject($key, $firstObject);

        // The second registry should not have any object under $key
        $this->assertFalse($secondRegistry->hasObject($key));
        $this->assertNull($secondRegistry->getObject($key));

        $secondObject = new \stdClass();
        $secondObject->value = "second";

        $secondRegistry->setObject($key, $secondObject);

        // Both registries should now have different objects under the same key
        $this->assertNotSame($firstRegistry->getObject($key), $secondRegistry->getObject($key));
        $this->assertSame($firstObject, $firstRegistry->getObject($key));
        $this->assertSame($secondObject, $secondRegistry->getObject($key));
    }

    public function testOperationsInOneInstanceDontAffectTheOther(): void
    {
        $registryA = new ObjectsRegistryFixture();
        $registryB = new ObjectsRegistryFixture();

        $registryA->setObject("keyA1", (object) ["data" => "A1"]);
        $registryA->setObject("keyA2", (object) ["data" => "A2"]);

        $registryB->setObject("keyB1", (object) ["data" => "B1"]);

        // Check presence in each registry
        $this->assertTrue($registryA->hasObject("keyA1"));
        $this->assertTrue($registryA->hasObject("keyA2"));
        $this->assertFalse($registryA->hasObject("keyB1"));

        $this->assertTrue($registryB->hasObject("keyB1"));
        $this->assertFalse($registryB->hasObject("keyA1"));
        $this->assertFalse($registryB->hasObject("keyA2"));

        // Flush one registry
        $registryA->flush();

        $this->assertFalse($registryA->hasObject("keyA1"));
        $this->assertFalse($registryA->hasObject("keyA2"));
        // Registry B should still have its object
        $this->assertTrue($registryB->hasObject("keyB1"));
    }
}
