<?php
declare(strict_types=1);

/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

namespace Charcoal\Base\Registry;

/**
 * Provides an abstract base class for implementing a singleton instance pattern.
 *
 * This class uses a static instance registry to manage singleton instances of the class.
 * It includes utility methods for accessing, creating, and replacing instances.
 * @method static getInstance()
 */
class AbstractClassSingleton
{
    use StaticObjectsRegistryTrait;

    /**
     * @return static The singleton instance of the called class.
     * @throws \RuntimeException If the instance is not found in the registry.
     */
    final protected static function getInstanceOrThrow(): static
    {
        if (!isset(static::$instances[static::class])) {
            throw new \RuntimeException("Instance of " . static::class . " not found");
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
     * Constructor method made protected by default
     */
    protected function __construct()
    {
    }

    /**
     * @param string $key
     * @return string
     */
    final protected static function normalizeRegistryKey(string $key): string
    {
        return $key;
    }
}