<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Concerns;

/**
 * This trait allows storing, retrieving, checking for existence, and removing
 * object instances by a string key. The key can be normalized for consistent
 * access, ensuring predictable behavior when interacting with the registry.
 * @template T of object
 * @property array<string, T> $instances
 */
trait InstancedObjectsRegistry
{
    /** @var array<string,T> */
    protected array $instances = [];

    use RequiresNormalizedRegistryKeys;

    /**
     * @param string $key
     * @param T $instance
     * @return void
     * @api
     */
    final protected function registrySetInstance(string $key, object $instance): void
    {
        $this->instances[$this->normalizeRegistryKey($key)] = $instance;
    }

    /**
     * @param string $key
     * @return T|null
     * @api
     */
    final protected function registryGetInstance(string $key): ?object
    {
        return $this->instances[$this->normalizeRegistryKey($key)] ?? null;
    }

    /**
     * @param string $key
     * @return bool
     * @api
     */
    final protected function registryHasInstance(string $key): bool
    {
        return array_key_exists($this->normalizeRegistryKey($key), $this->instances);
    }

    /**
     * @param string $key
     * @return void
     * @api
     */
    final protected function registryUnsetInstance(string $key): void
    {
        unset($this->instances[$this->normalizeRegistryKey($key)]);
    }

    /**
     * @return void
     * @api
     */
    final protected function registryFlush(): void
    {
        $this->instances = [];
    }

    /**
     * @return int
     * @api
     */
    final protected function registryCount(): int
    {
        return count($this->instances);
    }
}