<?php
declare(strict_types=1);

/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

namespace Charcoal\Base\Tests\Fixtures\Registry;

use Charcoal\Base\Support\SharedStaticInstances;

class SingletonRegistryOneFixture extends SharedStaticInstances
{
    public static function getInstance(): static
    {
        return static::getInstanceOrThrow();
    }

    public static function getInstanceNullable(): ?static
    {
        return static::getInstanceOrNull();
    }

    public static function createInstance(string $titleStr): static
    {
        return static::createOrReplaceInstance($titleStr);
    }

    public static function resetInstance(): void
    {
        static::registryFlush();
    }

    public static function hasInstance(string $classname): bool
    {
        return static::registryHasInstance($classname);
    }

    public static function count(): int
    {
        return static::registryCount();
    }

    public static function getClassnames(): array
    {
        return array_keys(static::registryReturnAll());
    }

    protected function __construct(public readonly string $titleStr)
    {
        parent::__construct();
    }
}