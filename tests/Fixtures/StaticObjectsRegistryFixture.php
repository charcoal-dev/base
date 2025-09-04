<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Tests\Fixtures;

use Charcoal\Base\Registry\Traits\StaticObjectsRegistry;

/**
 * A fixture class for managing a static registry of objects. Provides utility methods
 * for working with the registry, including storing, retrieving, checking, and clearing objects.
 */
class StaticObjectsRegistryFixture
{
    use StaticObjectsRegistry;

    public static function getObject(string $key): ?object
    {
        return static::registryGetInstance($key);
    }

    public static function setObject(string $key, object $object): void
    {
        static::registrySetInstance($key, $object);
    }

    public static function hasObject(string $key): bool
    {
        return static::registryHasInstance($key);
    }

    public static function unsetObject(string $key): void
    {
        static::registryUnsetInstance($key);
    }

    public static function flush(): void
    {
        static::registryFlush();

    }

    public static function count(): int
    {
        return static::registryCount();
    }

    public static function getAll(): array
    {
        return static::registryReturnAll();
    }

    protected static function normalizeRegistryKey(string $key): string
    {
        return strtolower($key);
    }
}