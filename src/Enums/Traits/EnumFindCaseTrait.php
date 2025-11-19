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

    public static function find(string $case, bool $caseSensitive = true): ?static
    {
        if (!$caseSensitive) {
            $case = strtolower($case);
        }

        foreach (static::cases() as $c) {
            $c2 = static::isBackedEnum() ? $c->value : $c->name;
            if (!$caseSensitive) {
                $c2 = strtolower($c2);
            }

            if ($c2 === $case) {
                return $c;
            }
        }

        return null;
    }
}