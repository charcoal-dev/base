<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Objects\Traits;

/**
 * Provides functionality to prevent debugging output or dumping of class properties.
 * Intended to improve security and control over data handling.
 */
trait NoDumpTrait
{
    final public function __debugInfo(): array
    {
        return [static::class, spl_object_id($this)];
    }
}