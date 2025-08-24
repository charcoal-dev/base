<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Support\Helpers;

use Random\RandomException;

/**
 * A helper class for generating and managing UUIDs (Universally Unique Identifiers).
 * This class provides utilities for creating and manipulating UUIDs compliant with
 * the UUID version 4 specification, leveraging random number generation for creating
 * 128-bit identifiers.
 */
abstract readonly class UuidHelper
{
    /**
     * Validates a UUID string to ensure it conforms to the UUID version 4 specification.
     */
    public static function isValidUuid(string $uuid): bool
    {
        return preg_match("/\A[0-9a-f]{8}-[0-9a-f]{4}-[1-8][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}\z/i", $uuid) === 1;
    }

    /**
     * This method creates a 128-bit identifier based on random numbers, following
     * the structure and constraints defined by the UUID version 4 specification.
     * @throws RandomException
     */
    public static function uuid4(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
        return vsprintf("%s%s-%s-%s-%s-%s%s%s", str_split(bin2hex($data), 4));
    }

    /**
     * Generates a version 5 UUID based on the provided namespace and name.
     */
    public static function uuid5(string $namespace, string $name): string
    {
        $nsBytes = hex2bin(str_replace("-", "", $namespace));
        $hash = sha1($nsBytes . $name, true);
        $hash = substr($hash, 0, 16);
        $hash[6] = chr((ord($hash[6]) & 0x0f) | 0x50);
        $hash[8] = chr((ord($hash[8]) & 0x3f) | 0x80);
        return vsprintf("%s%s-%s-%s-%s-%s%s%s", str_split(bin2hex($hash), 4));
    }
}