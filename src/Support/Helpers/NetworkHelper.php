<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Support\Helpers;

/**
 * Provides functionalities for verifying if an IP address matches a given set
 * of CIDR notations.
 */
abstract readonly class NetworkHelper
{
    /**
     * Checks if the given IP address is valid and returns its version.
     */
    public static function isValidIp(string $ip): int|false
    {
        $bin = @inet_pton($ip);
        if ($bin === false) {
            return false;
        }

        return strlen($bin) === 4 ? 4 : 6;
    }

    /**
     * Matches a given IP address against a list of CIDR notations to determine
     * if the IP falls within any of the specified ranges.
     */
    public static function ipInCidr(string $ip, array $cidrList): bool
    {
        $ipBin = @inet_pton($ip);
        if (!$ipBin) {
            return false;
        }

        $len = strlen($ipBin);
        foreach ($cidrList as $cidr) {
            [$net, $prefix] = array_pad(explode("/", $cidr, 2), 2, null);
            $netBin = @inet_pton($net ?? "");
            $pfx = is_numeric($prefix) ? (int)$prefix : -1;
            if ($netBin === false || strlen($netBin) !== $len || $pfx < 0 || $pfx > ($len * 8)) {
                continue;
            }

            $mask = self::mask($len, $pfx);
            if ((($ipBin & $mask) === ($netBin & $mask))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generates a binary mask for the specified number of bytes and bits.
     */
    private static function mask(int $bytes, int $bits): string
    {
        $full = intdiv($bits, 8);
        $rem = $bits % 8;
        $s = str_repeat("\xFF", $full);
        if ($bits % 8) {
            $s .= chr(0xFF << (8 - $rem) & 0xFF);
        }

        return str_pad($s, $bytes, "\x00", STR_PAD_RIGHT);
    }
}