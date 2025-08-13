<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Tests\Support;

use Charcoal\Base\Enums\Charset;
use Charcoal\Base\Enums\ExceptionAction;
use Charcoal\Base\Enums\FetchOrigin;
use Charcoal\Base\Enums\PrimitiveType;
use Charcoal\Base\Enums\StorageType;
use Charcoal\Base\Enums\ValidationState;
use Charcoal\Base\Support\Helpers\EnumHelper;
use Charcoal\Base\Vectors\StringVector;
use PHPUnit\Framework\TestCase;

/**
 * Tests for EnumHelper.
 */
final class EnumHelperTest extends TestCase
{
    private array $enums = [
        Charset::class,
        ExceptionAction::class,
        FetchOrigin::class,
        PrimitiveType::class,
        StorageType::class,
        ValidationState::class
    ];

    public function testIsStringBackedReturnsTrueForStringBackedEnum(): void
    {
        $enumClass = $this->findStringBackedEnumOrSkip();
        $this->assertTrue(EnumHelper::isStringBacked($enumClass));
    }

    public function testIsStringBackedReturnsFalseForIntBackedEnum(): void
    {
        $enumClass = $this->findIntBackedEnumOrSkip();
        $this->assertFalse(EnumHelper::isStringBacked($enumClass));
    }

    public function testIsStringBackedReturnsFalseForPureEnum(): void
    {
        $enumClass = $this->findPureEnumOrSkip();
        $this->assertFalse(EnumHelper::isStringBacked($enumClass));
    }

    public function testValidateStringBackedThrowsForNonExistingEnum(): void
    {
        $this->expectException(\LogicException::class);
        EnumHelper::validateStringBacked('NonExisting\\DefinitelyMissingEnum');
    }

    public function testValidateStringBackedThrowsForIntBackedEnum(): void
    {
        $enumClass = $this->findIntBackedEnumOrSkip();
        $this->expectException(\LogicException::class);
        EnumHelper::validateStringBacked($enumClass);
    }

    public function testValidateStringBackedThrowsForPureEnum(): void
    {
        $enumClass = $this->findPureEnumOrSkip();
        $this->expectException(\LogicException::class);
        EnumHelper::validateStringBacked($enumClass);
    }

    /**
     * @noinspection PhpUndefinedMethodInspection
     */
    public function testValidateEnumCasesReturnsValidatedUniqueValuesWithStringVector(): void
    {
        $enumClass = $this->findStringBackedEnumOrSkip();
        $cases = $enumClass::cases();
        $this->assertNotEmpty($cases);

        // Pick two valid values and duplicate the first one to test de-duplication
        $v1 = $cases[0]->value;
        $v2 = $cases[1]->value ?? $cases[0]->value; // in case there is only one, still valid
        $validated = EnumHelper::validateEnumCases($enumClass, ExceptionAction::Throw, $v1, $v1, $v2);

        // Expect unique, original order preserved
        $expected = array_values(array_unique([$v1, $v2]));
        $this->assertSame($expected, $validated);
    }

    /**
     * @noinspection PhpUndefinedMethodInspection
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function testValidateEnumCasesSkipsInvalidWhenOnInvalidIsNotThrow(): void
    {
        $enumClass = $this->findStringBackedEnumOrSkip();
        $cases = $enumClass::cases();
        $this->assertNotEmpty($cases);

        $valid = $cases[0]->value;
        $invalid = 'invalid-' . bin2hex(random_bytes(4));

        $onInvalid = $this->getNonThrowExceptionActionOrSkip();
        $vector = [$valid, $invalid, $valid];
        $validated = EnumHelper::validateEnumCases($enumClass, $onInvalid, ...$vector);

        // Invalid should be skipped, valid should remain (unique)
        $this->assertSame([$valid], $validated);
    }

    /**
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function testValidateEnumCasesThrowsOnInvalidWhenThrow(): void
    {
        $enumClass = $this->findStringBackedEnumOrSkip();
        $invalid = 'invalid-' . bin2hex(random_bytes(4));
        $vector = new StringVector(...[$invalid]);

        $this->expectException(\OutOfBoundsException::class);
        EnumHelper::validatedEnumCasesFromVector($enumClass, $vector, ExceptionAction::Throw);
    }

    private function findStringBackedEnumOrSkip(): string
    {
        foreach ($this->enums as $class) {
            if (!enum_exists($class)) {
                continue;
            }
            if (!is_a($class, \BackedEnum::class, true)) {
                continue;
            }
            $cases = $class::cases();
            if ($cases === [] || !property_exists($cases[0], 'value')) {
                continue;
            }
            if (is_string($cases[0]->value)) {
                return $class;
            }
        }

        $this->markTestSkipped('No suitable string-backed enum found in the Enums namespace.');
    }

    private function findIntBackedEnumOrSkip(): string
    {
        foreach ($this->enums as $class) {
            if (!enum_exists($class)) {
                continue;
            }
            if (!is_a($class, \BackedEnum::class, true)) {
                continue;
            }
            $cases = $class::cases();
            if ($cases === [] || !property_exists($cases[0], 'value')) {
                continue;
            }
            if (is_int($cases[0]->value)) {
                return $class;
            }
        }

        $this->markTestSkipped('No suitable int-backed enum found in the Enums namespace.');
    }

    private function findPureEnumOrSkip(): string
    {
        foreach ($this->enums as $class) {
            if (!enum_exists($class)) {
                continue;
            }
            if (!is_a($class, \BackedEnum::class, true)) {
                return $class; // Pure (unit) enum
            }
        }

        $this->markTestSkipped('No suitable pure enum found in the Enums namespace.');
    }

    private function getNonThrowExceptionActionOrSkip(): ExceptionAction
    {
        foreach (ExceptionAction::cases() as $case) {
            if ($case !== ExceptionAction::Throw) {
                return $case;
            }
        }

        $this->markTestSkipped('ExceptionAction enum does not provide a non-Throw case to test skip behavior.');
    }
}
