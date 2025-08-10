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
 */
abstract class AbstractFactoryRegistry
{
    private array $instances = [];

    use RequiresNormalizedRegistryKeys;

    abstract protected function create(string $key, ?\Closure $callback): object;

    protected function store(object $instance, string $key): object
    {
        $this->instances[$key] = $instance;
        return $instance;
    }

    /**
     * @api
     */
    protected function getExistingOrCreate(string $key, ?\Closure $callback): object
    {
        $key = $this->normalizeRegistryKey($key);
        if (isset($this->instances[$key])) {
            return $this->instances[$key];
        }

        return $this->store($this->create($key, $callback), $key);
    }
}