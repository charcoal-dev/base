<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Enums\Traits;

/**
 * This trait includes methods for determining if the implementing class
 * is a backed enum, retrieving enum case mappings, and extracting keys
 * or values from enum definitions.
 * @mixin \UnitEnum
 */
trait EnumMappingTrait
{
    /**
     * @return bool
     */
    protected static function isBackedEnum(): bool
    {
        return is_subclass_of(static::class, \BackedEnum::class);
    }

    /**
     * @return array<string, string|int|null>
     */
    protected static function getCaseMap(): array
    {
        $options = [];
        foreach (static::cases() as $case) {
            $options[$case->name] = $case->value ?? null;
        }

        return $options;
    }

    /**
     * @return array<string>|array<string|int>
     */
    public static function getCases(): array
    {
        return static::isBackedEnum() ?
            static::getCaseNames() : static::getCaseValues();
    }

    /**
     * @return array<string>
     */
    public static function getCaseNames(): array
    {
        return array_keys(static::getCaseMap());
    }

    /**
     * @return null|array<string|int>
     */
    public static function getCaseValues(): ?array
    {
        return static::isBackedEnum() ?
            array_values(static::getCaseMap()) : null;
    }
}