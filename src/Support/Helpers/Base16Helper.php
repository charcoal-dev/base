<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Support\Helpers;


use Charcoal\Base\Enums\ExceptionAction;

/**
 * A helper class that provides utility methods for working with base16.
 */
class Base16Helper
{
    /**
     * @param string $input
     * @return bool
     */
    public static function isBase16Encoded(string $input): bool
    {
        if (!$input) {
            return false;
        }

        if (str_starts_with($input, "0x")) {
            $input = substr($input, 2);
        }

        return ctype_xdigit($input);
    }

    /**
     * @param string $input
     * @return bool
     */
    public static function isHexadecimalString(string $input): bool
    {
        return static::isBase16Encoded($input);
    }

    /**
     * @param string $str
     * @param ExceptionAction $onFail
     * @return string|false
     */
    public static function decodeToBinary(string $str, ExceptionAction $onFail = ExceptionAction::Throw): string|false
    {
        return match (true) {
            !$str, !static::isBase16Encoded($str) => false,
            (strlen($str) % 2 !== 0) => hex2bin("0" . $str),
            default => hex2bin($str),
        } ?: ((match ($onFail) {
            ExceptionAction::Throw => throw new \InvalidArgumentException("Invalid hexadecimal string"),
            default => false,
        }));
    }
}