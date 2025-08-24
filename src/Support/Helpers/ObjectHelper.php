<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Support\Helpers;

/**
 * A helper class that provides utility methods for working with objects and class paths.
 */
abstract readonly class ObjectHelper
{
    /**
     * Checks if the given class or namespace path is valid.
     */
    public static function isValidClassname(string $path): bool
    {
        if (empty($path)) return false;
        $path = ltrim($path, "\\");
        return (bool)preg_match("/^(?:[A-Za-z_][A-Za-z0-9_]*\\\\)*[A-Za-z_][A-Za-z0-9_]*$/", $path);
    }

    /**
     * Checks if the given class path is valid and exists.
     */
    public static function isValidClass(string $path): bool
    {
        return self::isValidClassname($path) && class_exists($path);
    }

    /**
     * Returns the base class name of the given class.
     */
    public static function baseClassName(object|string $class): string
    {
        $fqCn = ltrim(is_object($class) ? $class::class : $class, "\\");
        $pos = strrpos($fqCn, "\\");
        return $pos === false ? $fqCn : substr($fqCn, $pos + 1);
    }

    /**
     * Breaks an object into an array comprising all scalar properties.
     * Works recursively on nested objects.
     */
    public static function break(object $object, int $maxDepth = 10, ?string $recursiveItemLabel = null): array
    {
        return DtoHelper::createFrom($object, $maxDepth, true, true, $recursiveItemLabel);
    }
}