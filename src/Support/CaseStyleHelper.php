<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Support;

/**
 * A helper class that provides utility methods for working with case styles.
 */
class CaseStyleHelper
{
    /**
     * @param string $input
     * @return string
     */
    public static function pascalCaseFromRaw(string $input): string
    {
        return ucfirst(implode("", static::splitStringOnNonWordChars($input)));
    }

    /**
     * @param string $input
     * @return string
     */
    public static function camelCaseFromStyled(string $input): string
    {
        return implode("", static::splitStringOnCaseChange($input));
    }

    /**
     * @param string $input
     * @param string $glue
     * @return string
     */
    public static function delimiterCaseFromRaw(string $input, string $glue = "_"): string
    {
        $words = static::splitStringOnNonWordChars($input);
        return $words ? strtolower(implode($glue, $words)) : "";
    }

    /**
     * @param string $input
     * @param string $glue
     * @return string
     */
    public static function delimiterCaseFromStyled(string $input, string $glue = "_"): string
    {
        $words = static::splitStringOnCaseChange($input);
        return $words ? strtolower(implode($glue, $words)) : "";
    }

    /**
     * @param string $input
     * @return array
     */
    protected static function splitStringOnNonWordChars(string $input): array
    {
        if ($input === "") {
            return [];
        }

        preg_match_all('/[A-Za-z0-9]+/', $input, $m);
        if (empty($m[0])) {
            return [];
        }

        return array_map(static fn(string $w) => ucfirst(strtolower($w)), $m[0]);
    }

    /**
     * @param string $input
     * @return array
     */
    protected static function splitStringOnCaseChange(string $input): array
    {
        if ($input === "") {
            return [];
        }

        preg_match_all('/[A-Z]?[a-z]+|[A-Z]+(?![a-z])|\d+/', $input, $m);
        $tokens = $m[0] ?? [];
        if (!$tokens) {
            return [];
        }

        $out = [];
        foreach ($tokens as $i => $t) {
            $t = strtolower($t);
            $out[] = $i === 0 ? $t : ucfirst($t);
        }

        return $out;
    }
}