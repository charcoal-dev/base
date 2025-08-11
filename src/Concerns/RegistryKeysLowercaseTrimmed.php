<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Concerns;

/**
 * @see RequiresNormalizedRegistryKeys
 */
trait RegistryKeysLowercaseTrimmed
{
    protected function normalizeRegistryKey(string $key): string
    {
        return strtolower(trim($key));
    }
}