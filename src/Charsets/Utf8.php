<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Charsets;

use Charcoal\Base\Contracts\Charsets\UnicodeLanguageRangeInterface;

/**
 * The Utf8 class provides utility methods for working with UTF-8 strings.
 */
class Utf8
{
    /**
     * @param string $unsafeString
     * @param bool $allowSpaces
     * @param bool $asciiCharset
     * @param UnicodeLanguageRangeInterface ...$languages
     * @return bool
     */
    public static function validate(
        string                        $unsafeString,
        bool                          $allowSpaces = true,
        bool                          $asciiCharset = true,
        UnicodeLanguageRangeInterface ...$languages
    ): bool
    {
        return (bool)preg_match("/^[" . static::generateRegExp($allowSpaces, $asciiCharset,
                ...$languages) . "]+$/u", $unsafeString);
    }

    /**
     * @param string $input
     * @param bool $allowSpaces
     * @param bool $asciiCharset
     * @param UnicodeLanguageRangeInterface ...$languages
     * @return string
     */
    public static function filterOutExtras(
        string                        $input,
        bool                          $allowSpaces = true,
        bool                          $asciiCharset = true,
        UnicodeLanguageRangeInterface ...$languages
    ): string
    {
        return preg_replace("/[^" . static::generateRegExp($allowSpaces, $asciiCharset,
                ...$languages) . "]+/u", "", $input);
    }

    /**
     * @param bool $allowSpaces
     * @param bool $asciiCharset
     * @param UnicodeLanguageRangeInterface ...$languages
     * @return string
     */
    protected static function generateRegExp(
        bool                          $allowSpaces = true,
        bool                          $asciiCharset = true,
        UnicodeLanguageRangeInterface ...$languages
    ): string
    {
        return ($allowSpaces ? "\x20" : "") .
            ($asciiCharset ? "\x21-\x7E" : "") .
            static::getCompiledUnicodeRange(...$languages);
    }

    /**
     * @param UnicodeLanguageRangeInterface ...$languages
     * @return string
     */
    protected static function getCompiledUnicodeRange(
        UnicodeLanguageRangeInterface ...$languages
    ): string
    {
        $unicodeCharsets = "";
        foreach ($languages as $charset) {
            if ($charset instanceof UnicodeLanguageRangeInterface) {
                $unicodeCharsets .= implode("", $charset->getUnicodeRange());
            }
        }

        return $unicodeCharsets;
    }
}