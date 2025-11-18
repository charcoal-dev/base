<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Encoding;

use Charcoal\Contracts\Buffers\ReadableBufferInterface;
use Charcoal\Contracts\Encoding\EncodingSchemeInterface;

/**
 * Enum representing different encoding schemes with their corresponding base values.
 * Implements the EncodingSchemeInterface to provide encoding and decoding functionalities.
 */
enum Encoding: int implements EncodingSchemeInterface
{
    case Base16 = 16;
    case Base32 = 32;
    case Base64 = 64;
    case Base64Url = 641;

    /**
     * Check if a string is encoded.
     */
    public function isEncoded(string $str): bool
    {
        return match ($this) {
            self::Base16 => Base16::isEncoded($str),
            self::Base32 => Base32::isEncoded($str),
            self::Base64 => Base64::isEncoded($str),
            self::Base64Url => Base64Url::isEncoded($str),
        };
    }

    /**
     * Encode a string or ReadableBufferInterface.
     */
    public function encode(ReadableBufferInterface|string $raw): string
    {
        return match ($this) {
            self::Base16 => Base16::encode($raw),
            self::Base32 => Base32::encode($raw),
            self::Base64 => Base64::encode($raw),
            self::Base64Url => Base64Url::encode($raw),
        };
    }

    /**
     * Decode a string.
     */
    public function decode(string $encoded): string
    {
        return match ($this) {
            self::Base16 => Base16::decode($encoded),
            self::Base32 => Base32::decode($encoded),
            self::Base64 => Base64::decode($encoded),
            self::Base64Url => Base64Url::decode($encoded),
        };
    }
}