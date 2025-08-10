<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Traits;

/**
 * This trait includes methods for determining if the implementing class
 * is a backed enum, retrieving enum case mappings, and extracting keys
 * or values from enum definitions.
 */
trait EnumOptionsTrait
{
    protected static function isBackedEnum(): bool
    {
        return is_subclass_of(static::class, \BackedEnum::class);
    }

    protected static function getEnumMap(): array
    {
        $options = [];
        foreach (static::cases() as $case) {
            $options[$case->name] = $case->value ?? null;
        }

        return $options;
    }

    public static function getOptions(): array
    {
        return static::isBackedEnum() ?
            static::getEnumValues() : static::getEnumKeys();
    }

    public static function getEnumKeys(): array
    {
        return array_keys(static::getEnumMap());
    }

    public static function getEnumValues(): ?array
    {
        return static::isBackedEnum() ?
            array_values(static::getEnumMap()) : null;
    }
}