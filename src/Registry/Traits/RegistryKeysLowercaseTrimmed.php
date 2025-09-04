<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Registry\Traits;

use Charcoal\Base\Registry\Concerns\RequiresNormalizedRegistryKeys;

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