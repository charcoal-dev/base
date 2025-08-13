<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Tests;

use Charcoal\Base\Support\Helpers\ObjectHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ObjectHelperTest extends TestCase
{
    public static function provideValidClassnames(): array
    {
        return [
            "simple class" => ["Foo"],
            "leading backslash trimmed" => ["\\Foo"],
            "namespaced class" => ["Foo\\Bar"],
            "deep namespace" => ["Vendor\\Package\\ClassName"],
            "underscore allowed at start" => ["_Leading"],
            "underscores and digits" => ["Foo_1\\Bar2"],
            "multiple leading slashes ok" => ["\\\\\\Foo\\Bar"],
        ];
    }

    public static function provideInvalidClassnames(): array
    {
        return [
            "empty" => [""],
            "starts with digit" => ["1Foo"],
            "segment starts with digit" => ["Foo\\1Bar"],
            "trailing namespace separator" => ["Foo\\"],
            "double separator end" => ["Foo\\\\"],
            "illegal char $" => ["Foo\\Bar$"],
            "only backslashes" => ["\\\\\\"],
        ];
    }

    #[DataProvider("provideValidClassnames")]
    public function testIsValidClassnameAcceptsValidNames(string $name): void
    {
        $this->assertTrue(ObjectHelper::isValidClassname($name));
    }

    #[DataProvider("provideInvalidClassnames")]
    public function testIsValidClassnameRejectsInvalidNames(string $name): void
    {
        $this->assertFalse(ObjectHelper::isValidClassname($name));
    }

    public function testIsValidClassDetectsExistingClasses(): void
    {
        // Built-in class
        $this->assertTrue(ObjectHelper::isValidClass(\stdClass::class));

        // Test fixtures (declared above)
        $this->assertTrue(ObjectHelper::isValidClass('Charcoal\Base\Tests\ObjectHelperTest'));
    }

    public function testIsValidClassRejectsNonExistingOrInvalid(): void
    {
        // Valid classname but class does not exist
        $this->assertFalse(ObjectHelper::isValidClass('Vendor\Package\MissingClass'));

        // Invalid classnames should also be rejected
        $this->assertFalse(ObjectHelper::isValidClass(""));
        $this->assertFalse(ObjectHelper::isValidClass("1Invalid"));
        $this->assertFalse(ObjectHelper::isValidClass("Foo\\1Bar"));
    }

    public function testBaseClassNameFromStrings(): void
    {
        $this->assertSame("Simple", ObjectHelper::baseClassName("Simple"));
        $this->assertSame("Simple", ObjectHelper::baseClassName("\\Simple"));
        $this->assertSame("ClassName", ObjectHelper::baseClassName("Vendor\\Package\\ClassName"));
        $this->assertSame("Bar", ObjectHelper::baseClassName("Foo\\Bar"));
    }

    public function testBaseClassNameFromObjects(): void
    {
        $this->assertSame("stdClass", ObjectHelper::baseClassName(new \stdClass()));
        $this->assertSame("CompositeTraitsFixture", ObjectHelper::baseClassName(new \Charcoal\Base\Tests\Fixtures\CompositeTraitsFixture("test", 1)));
    }
}