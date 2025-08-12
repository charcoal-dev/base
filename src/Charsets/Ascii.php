<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Charsets;

/**
 * The Ascii class provides utility methods for working with ASCII strings,
 * including validation and sanitization.
 */
class Ascii
{
    /**
     * @param string $in
     * @return bool
     */
    public static function isPrintableOnly(string $in): bool
    {
        return (bool)preg_match("/^[\x20-\x7E]*$/", $in);
    }

    /**
     * @param string $in
     * @return bool
     */
    public static function inCharset(string $in): bool
    {
        return (bool)preg_match("/^[\x00-\x7F]*$/", $in);
    }

    /**
     * @param string $unsafeString
     * @param bool $tabChar
     * @param bool $lineBreaks
     * @param bool $nullByte
     * @return string
     */
    public static function sanitizeUseRegEx(
        string $unsafeString,
        bool   $tabChar = false,
        bool   $lineBreaks = false,
        bool   $nullByte = false,
    ): string
    {
        return preg_replace("/[^\x20-\x7E" .
            ($tabChar ? "\t" : "") .
            ($lineBreaks ? "\n\r" : "") .
            ($nullByte ? "\x00" : "") . "]/", "", $unsafeString);
    }

    /**
     * @param string $unsafeString
     * @return string
     */
    public static function sanitizeUseFilter(string $unsafeString): string
    {
        return filter_var($unsafeString, FILTER_UNSAFE_RAW,
            FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
    }

    /**
     * @param string $unsafeString
     * @param string $allowChars
     * @param string $denyChars
     * @return string
     */
    public static function sanitizeByByte(
        string $unsafeString,
        string $allowChars = "",
        string $denyChars = ""
    ): string
    {
        if (strlen($unsafeString) === 0) {
            return "";
        }

        $allowChars = array_fill_keys(array_unique(array_map("ord", str_split($allowChars, 1))), true);
        $denyChars = array_fill_keys(array_unique(array_map("ord", str_split($denyChars, 1))), true);
        $length = strlen($unsafeString);

        $safeString = "";
        for ($i = 0; $i < $length; $i++) {
            $chr = $unsafeString[$i];
            $ord = ord($chr);
            if ($ord < 32 || $ord > 126) {
                if (isset($allowChars[$ord])) {
                    $safeString .= $unsafeString[$i];
                }

                continue;
            }

            if (isset($denyChars[$ord])) {
                continue;
            }

            $safeString .= $unsafeString[$i];
        }

        return $safeString;
    }
}