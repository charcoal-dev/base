<?php
declare(strict_types=1);

/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

namespace Charcoal\Base\Tests\Registry;

use Charcoal\Base\Registry\AbstractClassSingleton;
use Charcoal\Base\Tests\Fixtures\Registry\SingletonChildOneFixture;
use Charcoal\Base\Tests\Fixtures\Registry\SingletonChildThreeFixture;
use Charcoal\Base\Tests\Fixtures\Registry\SingletonChildTwoFixture;
use Charcoal\Base\Tests\Fixtures\Registry\SingletonRegistryOneFixture;
use Charcoal\Base\Tests\Fixtures\Registry\SingletonRegistryTwoFixture;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Class SingletonTest
 *
 * This test class verifies the behavior of singleton instances
 * across a collection of test cases. It ensures that singleton
 * objects retain consistent state, behaviors, and independence
 * among various implementations and subclasses.
 */
class ClassSingletonTest extends TestCase
{
    /**
     * Test that calling getInstance() before the instance is created
     * triggers an exception for classes that explicitly require creation first.
     */
    public function testGetInstanceWithoutCreateShouldThrow(): void
    {
        // Assuming another test might have left a leftover instance, reset the static store if needed.
        // For demonstration, we'll just do it in a fresh instance context.
        $this->expectException(\RuntimeException::class);
        SingletonRegistryOneFixture::getInstance();
    }

    /**
     * Test creating and fetching the same singleton instance,
     * verifying that the object ID remains consistent.
     */
    public function testSingletonRetainsSameObjectId(): void
    {
        // Create the instance
        $instance = SingletonRegistryOneFixture::createInstance("FirstTitle");
        $firstId = spl_object_id($instance);

        // Retrieve the instance again
        $retrieved = SingletonRegistryOneFixture::getInstance();
        $secondId = spl_object_id($retrieved);

        // The object IDs should match
        $this->assertSame($firstId, $secondId,
            "SingletonRegistry1::getInstance() should yield the same object reference");
    }

    /**
     * Test overwriting an existing singleton instance
     * and check that the object ID changes when replaced.
     */
    public function testOverwritingSingletonChangesObjectId(): void
    {
        // Ensure there's an existing instance
        SingletonRegistryOneFixture::createInstance("OriginalTitle");
        $oldInstance = SingletonRegistryOneFixture::getInstance();
        $oldId = spl_object_id($oldInstance);

        // Overwrite with a new instance
        $newInstance = SingletonRegistryOneFixture::createInstance("NewTitle");
        $newId = spl_object_id($newInstance);

        // The old and new object IDs should differ
        $this->assertNotSame($oldId, $newId,
            "Overwriting the existing instance should produce a new object reference");
    }

    /**
     * Test that a child class maintains its own singleton, distinct from the parent.
     */
    public function testChildClassOwnSingleton(): void
    {
        // Create an instance in the parent
        $parent = SingletonRegistryOneFixture::createInstance("ParentTitle");
        $parentId = spl_object_id($parent);

        // Create an instance in the child
        $child = SingletonChildOneFixture::createInstance("ChildTitle");
        $childId = spl_object_id($child);

        // The IDs should differ because the parent and child each have a separate singleton
        $this->assertNotSame($parentId, $childId,
            "Parent and child singletons should be different objects");

        $this->assertEquals("ParentTitle", SingletonRegistryOneFixture::getInstance()->titleStr);
        $this->assertEquals("ChildTitle", SingletonChildOneFixture::getInstance()->titleStr);
    }

    /**
     * Test optional parameters in SingletonRegistryTwo.
     * Spl_object_id helps confirm that repeated calls return the same object.
     */
    public function testRegistryTwoOptionalParamRetainsSingleton(): void
    {
        $firstInstance = SingletonRegistryTwoFixture::getInstance(null);
        $firstId = spl_object_id($firstInstance);

        // Even though we call getInstance with a new string, it should return the original instance
        $secondInstance = SingletonRegistryTwoFixture::getInstance("NewParam");
        $secondId = spl_object_id($secondInstance);

        $this->assertSame($firstId, $secondId,
            "SingletonRegistryTwo should maintain the same object reference despite new parameters after creation");
    }

    /**
     * Test child classes of different parents to ensure each has its own distinct instance.
     */
    public function testMultipleChildClassesHaveIndependentSingletons(): void
    {
        // Create ChildOne, ChildTwo, ChildThree (Two is from SingletonRegistry1, Three from SingletonRegistryTwo)
        $childOne = SingletonChildOneFixture::createInstance("ChildOneTitle");
        $childTwo = SingletonChildTwoFixture::createInstance("ChildTwoTitle");
        $childThree = SingletonChildThreeFixture::getInstance("ChildThreeTitle");

        // Compare object IDs among the three children
        $this->assertNotSame(
            spl_object_id($childOne),
            spl_object_id($childTwo),
            "ChildOne and ChildTwo singletons should be distinct objects"
        );

        $this->assertNotSame(
            spl_object_id($childOne),
            spl_object_id($childThree),
            "ChildOne and ChildThree singletons should be distinct objects"
        );

        $this->assertNotSame(
            spl_object_id($childTwo),
            spl_object_id($childThree),
            "ChildTwo and ChildThree singletons should be distinct objects"
        );
    }

    /**
     * Tests that repeated calls always return the same object
     * and do not further mutate $instances after it has been set.
     */
    public function testRepeatedCallsDoNotChangeRegistry(): void
    {
        $first = SingletonRegistryTwoFixture::getInstance("FirstCall");
        $firstId = spl_object_id($first);

        // Call it multiple times with different arguments
        for ($i = 0; $i < 3; $i++) {
            $instance = SingletonRegistryTwoFixture::getInstance("Param" . $i);
            $this->assertSame($first, $instance, "All calls should reference the same singleton instance.");
            $this->assertSame($firstId, spl_object_id($instance), "Object IDs should remain the same across repeated calls.");
        }
    }

    /**
     * Test that the "instances" property is private and not visible in child classes.
     */
    public function testInstancesPropertyScopeAndInheritance(): void
    {
        $abstract = AbstractClassSingleton::class;
        $child = SingletonRegistryOneFixture::class;

        // Check child is a subclass of abstract singleton
        $this->assertTrue(
            is_subclass_of($child, $abstract),
            "$child should extend $abstract"
        );

        // Check "instances" property exists and is private in abstract class
        $refAbstract = new ReflectionClass($abstract);
        $prop = $refAbstract->getProperty("instances");
        $this->assertTrue(
            $prop->isPrivate(),
            'instances" should be private in AbstractClassSingleton'
        );

        // Check that property "instances" is NOT visible in the child (should throw)
        $this->expectException(\ReflectionException::class);
        (new ReflectionClass($child))->getProperty("instances");
    }

    /**
     * Test concurrency-like scenario with multiple later calls:
     * quickly repeated getInstance() calls in a loop to ensure
     * only one instance is created.
     */
    public function testFastLoopCallsProduceOnlyOneInstance(): void
    {
        $first = null;
        $firstId = null;

        for ($i = 1; $i <= 50; $i++) {
            $inst = SingletonRegistryTwoFixture::getInstance("Title" . $i);

            // The very first iteration sets our reference instance
            if ($i === 1) {
                $first = $inst;
                $firstId = spl_object_id($inst);
                continue;
            }

            // All subsequent iterations should match the first
            $this->assertSame($first, $inst, "Iteration $i should yield the same instance.");
            $this->assertSame($firstId, spl_object_id($inst), "Iteration $i should match the first object's ID.");
        }
    }

    /**
     * Test the scenario where null is intentionally passed for the constructor argument,
     * verifying that it doesn't break object creation, and we still have a valid instance.
     */
    public function testNullConstructorArgument(): void
    {
        $instance = SingletonRegistryTwoFixture::getInstance(null);
        $this->assertNull($instance->titleStr, "titleStr should be null if passed as null during creation.");

        // Ensure repeated calls with non-null strings don’t create a fresh instance
        $again = SingletonRegistryTwoFixture::getInstance("IgnoredTitle");
        $this->assertSame($instance, $again, "Subsequent calls with any param should return the same instance.");
        $this->assertNull($again->titleStr, "titleStr should remain null in the same object instance.");
    }

    /**
     */
    public function testResetInstanceCreatesNewSingleton(): void
    {
        $first = SingletonRegistryOneFixture::createInstance("A");
        SingletonRegistryOneFixture::resetInstance(); // You need to support this in your singleton
        $second = SingletonRegistryOneFixture::createInstance("B");
        $this->assertNotSame($first, $second, "Reset should allow new instance creation.");
    }
}