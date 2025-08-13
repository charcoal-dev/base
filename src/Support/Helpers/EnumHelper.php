<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Support\Helpers;

use Charcoal\Base\Contracts\Vectors\StringVectorInterface;
use Charcoal\Base\Enums\ExceptionAction;

/**
 * Provides helper methods for handling operations related to enums.
 */
class EnumHelper
{
    /**
     * @param string $enumClass
     * @return bool
     */
    public static function isStringBacked(string $enumClass): bool
    {
        try {
            self::validateStringBacked($enumClass);
            return true;
        } catch (\LogicException) {
            return false;
        }
    }

    /**
     * @param \UnitEnum ...$enums
     * @return array
     */
    public static function filterUniqueFromSet(\UnitEnum ...$enums): array
    {
        if (!$enums) {
            return [];
        }

        $unique = [];
        foreach ($enums as $case) {
            $key = $case::class . "::" . $case->name;
            $unique[$key] ??= $case;
        }

        return array_values($unique);
    }

    /**
     * @param class-string<\BackedEnum> $enumClass
     * @return void
     */
    public static function validateStringBacked(string $enumClass): void
    {
        if (!enum_exists($enumClass)) {
            throw new \LogicException("Enum class does not exist: " . $enumClass);
        }

        if (!is_a($enumClass, \BackedEnum::class, true)) {
            throw new \LogicException("Enum class is not a backed enum: " . $enumClass);
        }

        if (!is_string($enumClass::cases()[0]->value)) {
            throw new \LogicException("Enum class is not a string-backed enum: " . $enumClass);
        }
    }

    /**
     * @param class-string<\BackedEnum> $enumClass
     * @param ExceptionAction $onInvalid
     * @param string[] $values
     * @return string[]
     */
    public static function validateEnumCases(
        string          $enumClass,
        ExceptionAction $onInvalid = ExceptionAction::Throw,
        string          ...$values
    ): array
    {
        return static::validateEnumCasesInternal($onInvalid, $enumClass, array_unique($values));
    }

    /**
     * @param class-string<\BackedEnum> $enumClass
     * @param StringVectorInterface $vector
     * @param ExceptionAction $onInvalid
     * @return string[]
     */
    public static function validatedEnumCasesFromVector(
        string                $enumClass,
        StringVectorInterface $vector,
        ExceptionAction       $onInvalid = ExceptionAction::Throw,
    ): array
    {
        return static::validateEnumCasesInternal($onInvalid, $enumClass,
            $vector->filterUnique()->getArray());
    }

    /**
     * @param ExceptionAction $onInvalid
     * @param class-string<\BackedEnum> $enumClass
     * @param array $uniqueValues
     * @return array
     * @internal
     */
    protected static function validateEnumCasesInternal(
        ExceptionAction $onInvalid,
        string          $enumClass,
        array           $uniqueValues
    ): array
    {
        static::validateStringBacked($enumClass);

        $validated = [];
        foreach ($uniqueValues as $value) {
            if (!$enumClass::tryFrom($value)) {
                if ($onInvalid !== ExceptionAction::Throw) {
                    continue;
                }

                throw new \OutOfBoundsException(ObjectHelper::baseClassName($enumClass) .
                    " does not define: " . var_export($value, true));
            }

            $validated[] = $value;
        }

        return $validated;
    }
}