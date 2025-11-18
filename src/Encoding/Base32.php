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
 * A helper class that provides utility methods for working with base32.
 */
final readonly class Base32 implements EncodingSchemeStaticInterface
{
    /**
     * @param string $str
     * @return bool
     */
    public static function isEncoded(string $str): bool
    {
        return $str && preg_match('/^[A-Z2-7]+=*$/i', $str) === 1;
    }

    /**
     * @param ReadableBufferInterface|string $raw
     * @return string
     */
    public static function encode(ReadableBufferInterface|string $raw): string
    {
        $data = $raw instanceof ReadableBufferInterface ? $raw->bytes() : $raw;
        $alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ234567";
        $encoded = "";
        $bitBuffer = 0;
        $bitCount = 0;

        for ($i = 0; $i < strlen($data); $i++) {
            $bitBuffer = ($bitBuffer << 8) | ord($data[$i]);
            $bitCount += 8;

            while ($bitCount >= 5) {
                $index = ($bitBuffer >> ($bitCount - 5)) & 0x1F;
                $bitCount -= 5;
                $encoded .= $alphabet[$index];
            }
        }

        if ($bitCount > 0) {
            $encoded .= $alphabet[($bitBuffer << (5 - $bitCount)) & 0x1F];
        }

        while ((strlen($encoded) % 8) !== 0) {
            $encoded .= "=";
        }

        return $encoded;
    }

    /**
     * @param string $encoded
     * @return string
     */
    public static function decode(string $encoded): string
    {
        $encoded = rtrim($encoded, "=");
        $alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ234567";
        $decoded = "";
        $bitBuffer = 0;
        $bitCount = 0;

        for ($i = 0; $i < strlen($encoded); $i++) {
            $char = strtoupper($encoded[$i]);
            $pos = strpos($alphabet, $char);
            if ($pos === false) {
                throw new \InvalidArgumentException("Invalid character in Base32 string: " . $char);
            }

            $bitBuffer = ($bitBuffer << 5) | $pos;
            $bitCount += 5;

            if ($bitCount >= 8) {
                $bitCount -= 8;
                $decoded .= chr(($bitBuffer >> $bitCount) & 0xFF);
            }
        }

        return $decoded;
    }
}