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
 * A helper class that provides utility methods for working with base64url.
 */
final readonly class Base64Url implements EncodingSchemeStaticInterface
{
    /**
     * @param string $str
     * @return bool
     */
    public static function isEncoded(string $str): bool
    {
        return $str && preg_match('/^[A-Za-z0-9\-_]+={0,2}$/', $str) && in_array(strlen($str) % 4, [0, 2, 3]);
    }

    /**
     * @param ReadableBufferInterface|string $raw
     * @return string
     */
    public static function encode(ReadableBufferInterface|string $raw): string
    {
        $b64 = Base64::encode($raw);
        return strtr($b64, "+/", "-_");
    }

    /**
     * @param string $encoded
     * @return string
     */
    public static function decode(string $encoded): string
    {
        return match (true) {
            !$encoded, !self::isEncoded($encoded) => false,
            default => base64_decode(strtr($encoded, "-_", "+/") .
                str_repeat("=", (4 - strlen($encoded) % 4) % 4), true),
        } ?: throw new \InvalidArgumentException("Invalid base64 encoded string");
    }
}