<?php
declare(strict_types=1);

/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

namespace Charcoal\Base\Tests\Fixtures\Registry;

use Charcoal\Base\Registry\AbstractClassSingleton;

class SingletonRegistryTwoFixture extends AbstractClassSingleton
{
    public static function getInstance(?string $titleStr): static
    {
        return static::getInstanceOrConstruct($titleStr);
    }

    /**
     * @param class-string $classname
     * @return bool
     */
    public static function hasInstance(string $classname): bool
    {
        return static::registryHasInstance($classname);
    }

    public static function getClassnames(): array
    {
        return array_keys(static::registryReturnAll());
    }

    public static function getAll(): array
    {
        return static::registryReturnAll();
    }

    protected function __construct(public readonly ?string $titleStr)
    {
        parent::__construct();
    }
}