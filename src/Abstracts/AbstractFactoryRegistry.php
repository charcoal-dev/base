<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Abstracts;

use Charcoal\Base\Concerns\RequiresNormalizedRegistryKeys;

/**
 * Abstract base class for managing and creating instances of various objects.
 * Uses a normalized key-based storage mechanism to manage object instances.
 * @template T of object
 * @property array<string,T> $instances
 */
abstract class AbstractFactoryRegistry
{
    protected array $instances = [];

    use RequiresNormalizedRegistryKeys;

    /**
     * @param string $key
     * @return T
     */
    abstract protected function create(string $key): object;

    /**
     * @param string $key
     * @return T
     * @api
     */
    protected function getExistingOrCreate(string $key): object
    {
        $key = $this->normalizeRegistryKey($key);
        if (isset($this->instances[$key])) {
            return $this->instances[$key];
        }

        return $this->store($this->create($key), $key, true);
    }

    /**
     * @param object $instance
     * @param string $key
     * @param bool $normalized
     * @return T
     */
    protected function store(object $instance, string $key, bool $normalized): object
    {
        $key = $normalized ? $key : $this->normalizeRegistryKey($key);
        $this->instances[$key] = $instance;
        return $instance;
    }
}