<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Enums\Traits;

/**
 * @mixin \UnitEnum|\BackedEnum
 */
trait EnumFindCaseTrait
{
    use EnumMappingTrait;

    public static function find(string $case): ?static
    {
        foreach (static::cases() as $c) {
            $c2 = static::isBackedEnum() ? $c->value : $c->name;
            if ($c2 === $case) {
                return $c;
            }
        }

        return null;
    }
}