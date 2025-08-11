<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Support;

use Charcoal\Base\Contracts\Vectors\StringVectorProviderInterface;
use Charcoal\Base\Enums\ExceptionAction;
use Charcoal\Base\Vectors\AbstractTokenVector;
use Charcoal\Base\Vectors\StringVector;

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
     * @param StringVector|StringVectorProviderInterface $values
     * @param ExceptionAction $onInvalid
     * @return string[]
     */
    public static function validateEnumValues(
        string                                     $enumClass,
        StringVector|StringVectorProviderInterface $values,
        ExceptionAction                            $onInvalid = ExceptionAction::Throw
    ): array
    {
        static::validateStringBacked($enumClass);
        $values = $values->filterUnique()->getArray();
        $validated = [];
        foreach ($values as $value) {
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