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
abstract readonly class StringHelper
{
    /**
     * Cleans the multiple spaces in the given input string.
     */
    public static function cleanSpaces(string $input): string
    {
        return trim(preg_replace("/(\s+)/", " ", $input));
    }

    /**
     * Trims the given input if it is a string and returns null if the trimmed string is empty.
     * If the input is not a string, null is returned.
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