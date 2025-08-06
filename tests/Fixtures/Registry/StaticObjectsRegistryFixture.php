<?php
declare(strict_types=1);

/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

namespace Charcoal\Base\Tests\Fixtures\Registry;

use Charcoal\Base\Registry\StaticObjectsRegistryTrait;

class StaticObjectsRegistryFixture
{
    use StaticObjectsRegistryTrait;

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