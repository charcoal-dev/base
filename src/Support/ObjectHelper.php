<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Support;

/**
 * A helper class that provides utility methods for working with objects and class paths.
 */
class ObjectHelper
{
    /**
     * @param string $path
     * @return bool
     */
    public static function isValidClassname(string $path): bool
    {
        if (empty($path)) return false;
        $path = ltrim($path, "\\");
        return (bool)preg_match('/^(?:[A-Za-z_][A-Za-z0-9_]*\\\\)*[A-Za-z_][A-Za-z0-9_]*$/', $path);
    }

    /**
     * @param string $path
     * @return bool
     */
    public static function isValidClass(string $path): bool
    {
        return self::isValidClassname($path) && class_exists($path);
    }

    /**
     * @param object|class-string $class
     * @return string
     */
    public static function baseClassName(object|string $class): string
    {
        $fqCn = ltrim(is_object($class) ? $class::class : $class, "\\");
        $pos = strrpos($fqCn, "\\");
        return $pos === false ? $fqCn : substr($fqCn, $pos + 1);
    }

}