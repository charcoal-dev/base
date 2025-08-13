<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Support\Helpers;

/**
 * A helper class that provides utility methods for working with strings.
 */
class StringHelper
{
    /**
     * @param string $input
     * @return string
     */
    public static function cleanSpaces(string $input): string
    {
        return trim(preg_replace('/(\s+)/', ' ', $input));
    }

    /**
     * @param mixed $input
     * @return string|null
     */
    public static function getTrimmedOrNull(mixed $input): ?string
    {
        if (is_string($input)) {
            $input = trim($input);
            if (!$input) {
                return null;
            }

            return $input;
        }

        return null;
    }
}