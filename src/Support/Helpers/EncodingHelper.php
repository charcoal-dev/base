<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Support\Helpers;

/**
 * A helper class that provides utility methods for working with encoding.
 */
class EncodingHelper
{
    /**
     * @param string $input
     * @param bool $lengthCheck
     * @return bool
     */
    public static function isBase64Encoded(string $input, bool $lengthCheck): bool
    {
        return preg_match('/^[A-Za-z0-9+\/]+={0,2}$/', $input) === 1 &&
            (!$lengthCheck || strlen($input) % 4 === 0);
    }
}