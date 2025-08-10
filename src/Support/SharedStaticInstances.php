<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Support;

use Charcoal\Base\Concerns\StaticObjectsRegistry;

/**
 * This class uses a static instance registry to manage singleton instances of the class.
 * It includes utility methods for accessing, creating, and replacing instances.
 * @method static getInstance()
 */
class SharedStaticInstances
{
    use StaticObjectsRegistry;

    /**
     * @return static The singleton instance of the called class.
     * @throws \RuntimeException If the instance is not found in the registry.
     */
    final protected static function getInstanceOrThrow(): static
    {
        if (!isset(static::$instances[static::class])) {
            static::unregisteredInstance();
        }

        return static::$instances[static::class];
    }

    /**
     * @return static|null
     */
    final protected static function getInstanceOrNull(): ?static
    {
        return static::$instances[static::class] ?? null;
    }

    /**
     * @return static
     */
    protected static function getInstanceOrConstruct(): static
    {
        if (!isset(static::$instances[static::class])) {
            return static::createOrReplaceInstance(...func_get_args());
        }

        return static::$instances[static::class];
    }

    /**
     * @return static
     */
    protected static function createOrReplaceInstance(): static
    {
        static::$instances[static::class] = new static(...func_get_args());
        return static::$instances[static::class];
    }

    /**
     * @return never
     */
    protected static function unregisteredInstance(): never
    {
        throw new \RuntimeException("Instance of " . static::class . " not found");
    }

    /**
     * @param string $key
     * @return string
     */
    protected static function normalizeRegistryKey(string $key): string
    {
        return $key;
    }

    /**
     * Constructor method made protected by default
     */
    final protected function __construct()
    {
    }
}