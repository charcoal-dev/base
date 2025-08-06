<?php
declare(strict_types=1);

/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

namespace Charcoal\Base\Registry;

/**
 * This trait provides a mechanism for storing and accessing objects
 * in a centralized static registry. It supports adding, retrieving,
 * checking existence, removing, and normalizing instance keys in the registry.
 */
trait StaticObjectsRegistryTrait
{
    private static array $instances = [];

    final protected static function registrySetInstance(string $key, object $instance): void
    {
        static::$instances[static::normalizeRegistryKey($key)] = $instance;
    }

    final protected static function registryGetInstance(string $key): ?object
    {
        return static::$instances[static::normalizeRegistryKey($key)] ?? null;
    }

    final protected static function registryHasInstance(string $key): bool
    {
        return array_key_exists(static::normalizeRegistryKey($key), static::$instances);
    }

    final protected static function registryUnsetInstance(string $key): void
    {
        unset(static::$instances[static::normalizeRegistryKey($key)]);
    }

    final protected static function registryFlush(): void
    {
        static::$instances = [];
    }

    final protected static function registryCount(): int
    {
        return count(static::$instances);
    }

    final protected static function registryReturnAll(): array
    {
        return static::$instances;
    }

    abstract protected static function normalizeRegistryKey(string $key): string;
}