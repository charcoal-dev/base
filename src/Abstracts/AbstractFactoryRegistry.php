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
 * @var array<string,T> $instances
 */
abstract class AbstractFactoryRegistry
{
    private array $instances = [];

    use RequiresNormalizedRegistryKeys;

    /**
     * @param string $key
     * @return T
     */
    abstract protected function create(string $key): object;

    /**
     * @param T $instance
     * @param string $key
     * @return T
     */
    protected function store(object $instance, string $key): object
    {
        $this->instances[$key] = $instance;
        return $instance;
    }

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

        return $this->store($this->create($key), $key);
    }
}