<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Encoding;

use Charcoal\Contracts\Buffers\ReadableBufferInterface;
use Charcoal\Contracts\Encoding\EncodingSchemeStaticInterface;

/**
 * A helper class that provides utility methods for working with base16.
 */
final readonly class Base16 implements EncodingSchemeStaticInterface
{
    /**
     * @param string $str
     * @return bool
     */
    public static function isEncoded(string $str): bool
    {
        return match (true) {
            !$str => false,
            str_starts_with($str, "0x") => ctype_xdigit(substr($str, 2)),
            default => ctype_xdigit($str),
        };
    }

    /**
     * @param ReadableBufferInterface|string $raw
     * @return string
     */
    public static function encode(ReadableBufferInterface|string $raw): string
    {
        if ($raw instanceof ReadableBufferInterface) {
            $raw = $raw->bytes();
        }

        $b16 = bin2hex($raw);
        if (strlen($b16) % 2 !== 0) {
            $b16 = "0" . $b16;
        }

        return $b16;
    }

    /**
     * @param string $encoded
     * @return string
     */
    public static function decode(string $encoded): string
    {
        return match (true) {
            !$encoded, !self::isEncoded($encoded) => false,
            (strlen($encoded) % 2 !== 0) => hex2bin("0" . $encoded),
            default => hex2bin($encoded),
        } ?: throw new \InvalidArgumentException("Invalid hexadecimal string");
    }
}