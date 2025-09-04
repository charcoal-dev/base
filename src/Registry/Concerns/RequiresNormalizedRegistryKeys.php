<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Registry\Concerns;

/**
 * This trait requires the implementation of the `normalizeRegistryKey()` method with `protected` scope.
 * @internal
 */
trait RequiresNormalizedRegistryKeys
{
    abstract protected function normalizeRegistryKey(string $key): string;
}