<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Arrays;

/**
 * A helper class that provides utility methods for working with arrays.
 */
abstract readonly class ArrayHelper
{
    /**
     * @param array $data
     * @return array
     */
    public static function canonicalizeLexicographic(array $data): array
    {
        if (!self::isSequential($data)) {
            uksort($data, function ($a, $b) {
                if (ctype_digit((string)$a) && ctype_digit((string)$b)) {
                    return strnatcmp((string)$a, (string)$b);
                }

                return strcmp((string)$a, (string)$b);
            });
        }

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = self::canonicalizeLexicographic($value);
            }
        }

        return self::isSequential($data) ? $data : (count($data) === 0 ? [] : $data);
    }

    /**
     * @param array $data
     * @return bool
     */
    public static function isSequential(array $data): bool
    {
        return array_keys($data) === range(0, count($data) - 1);
    }

    /**
     * @param array $a
     * @param array $b
     * @return array
     */
    public static function mergeAssocDeep(array $a, array $b): array
    {
        foreach ($b as $key => $value) {
            if (isset($a[$key]) && is_array($a[$key]) && is_array($value)
                && array_is_list($a[$key]) === false && array_is_list($value) === false) {
                $a[$key] = self::mergeAssocDeep($a[$key], $value);
            } else {
                $a[$key] = $value;
            }
        }

        return $a;
    }

    /**
     * @param array $array
     * @param int $limit
     * @return int
     */
    public static function checkDepth(array $array, int $limit = 0): int
    {
        if ($limit < 0) {
            throw new \InvalidArgumentException("Invalid depth limit to crosscheck: " . $limit);
        }

        return self::checkDepthRecursive($array, $limit, 0);
    }

    /**
     * @param array $array
     * @param int $limit
     * @param int $depth
     * @return int
     */
    private static function checkDepthRecursive(array $array, int $limit = 0, int $depth = 0): int
    {
        if ($limit > 0 && $depth >= $limit) {
            return $limit;
        }

        $maxDepth = $depth;
        foreach ($array as $value) {
            if (is_object($value)) {
                throw new \InvalidArgumentException("Object detected at depth: " . $depth);
            }

            if (is_array($value)) {
                $child = self::checkDepthRecursive($value, $limit, $depth + 1);
                if ($child > $maxDepth) {
                    $maxDepth = $child;
                    if ($limit > 0 && $maxDepth >= $limit) {
                        return $limit;
                    }
                }
            }
        }

        return $maxDepth;
    }
}