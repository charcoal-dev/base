<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Enums\Traits;

/**
 * @mixin \UnitEnum
 */
trait EnumFindCaseTrait
{
    public static function find(string $case): ?static
    {
        foreach (static::cases() as $c) {
            if ($c->name === $case) {
                return $c;
            }
        }

        return null;
    }
}