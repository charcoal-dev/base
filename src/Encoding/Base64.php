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
 * A helper class that provides utility methods for working with encoding.
 */
final readonly class Base64 implements EncodingSchemeStaticInterface
{
    /**
     * @param string $str
     * @return bool
     */
    public static function isEncoded(string $str): bool
    {
        return $str && preg_match('/^[A-Za-z0-9+\/]+={0,2}$/', $str) === 1 && strlen($str) % 4 === 0;
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

        return base64_encode($raw);
    }

    /**
     * @param string $encoded
     * @return string
     */
    public static function decode(string $encoded): string
    {
        return match (true) {
            !$encoded, !self::isEncoded($encoded) => false,
            default => base64_decode($encoded, true),
        } ?: throw new \InvalidArgumentException("Invalid hexadecimal string");
    }
}