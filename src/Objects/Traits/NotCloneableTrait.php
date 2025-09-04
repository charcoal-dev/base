<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Objects\Traits;

/**
 * Classes using this trait will throw a BadMethodCallException if an attempt is made
 * to clone an instance. This ensures that instances are strictly non-clonable.
 */
trait NotCloneableTrait
{
    final public function __clone(): never
    {
        throw new \BadMethodCallException(static::class . " instance cannot be cloned");
    }
}