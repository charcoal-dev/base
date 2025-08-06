<?php
declare(strict_types=1);

/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

namespace Charcoal\Base\Registry;

/**
 * This trait allows storing, retrieving, checking for existence, and removing
 * object instances by a string key. The key can be normalized for consistent
 * access, ensuring predictable behavior when interacting with the registry.
 */
trait ObjectsRegistryTrait
{
    private array $instances = [];

    final protected function registrySetInstance(string $key, object $instance): void
    {
        $this->instances[$this->normalizeRegistryKey($key)] = $instance;
    }

    final protected function registryGetInstance(string $key): ?object
    {
        return $this->instances[$this->normalizeRegistryKey($key)] ?? null;
    }

    final protected function registryHasInstance(string $key): bool
    {
        return array_key_exists($this->normalizeRegistryKey($key), $this->instances);
    }

    final protected function registryUnsetInstance(string $key): void
    {
        unset($this->instances[$this->normalizeRegistryKey($key)]);
    }

    final protected function registryFlush(): void
    {
        $this->instances = [];
    }

    final protected function registryCount(): int
    {
        return count($this->instances);
    }

    final protected function registryReturnAll(): array
    {
        return $this->instances;
    }

    abstract protected function normalizeRegistryKey(string $key): string;
}