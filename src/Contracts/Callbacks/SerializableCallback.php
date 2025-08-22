<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Contracts\Callbacks;

/**
 * Represents a contract for callback functions that can be serialized.
 * Classes implementing this interface must ensure their implementation
 * supports proper serialization and deserialization for callback usage.
 */
interface SerializableCallback
{
    public function invoke(): mixed;
}