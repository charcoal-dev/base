<?php
declare(strict_types=1);

/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

namespace Charcoal\Base\Tests\Fixtures\Registry;

use Charcoal\Base\Registry\ObjectsRegistryTrait;

class ObjectsRegistryFixture
{
    use ObjectsRegistryTrait;

    public function getObject(string $key): ?object
    {
        return $this->registryGetInstance($key);
    }

    public function setObject(string $key, object $object): void
    {
        $this->registrySetInstance($key, $object);
    }

    public function hasObject(string $key): bool
    {
        return $this->registryHasInstance($key);
    }

    public function unsetObject(string $key): void
    {
        $this->registryUnsetInstance($key);
    }

    public function flush(): void
    {
        $this->registryFlush();
    }

    public function count(): int
    {
        return $this->registryCount();
    }

    public function getAll(): array
    {
        return $this->registryReturnAll();
    }

    protected function normalizeRegistryKey(string $key): string
    {
        return strtolower($key);
    }
}