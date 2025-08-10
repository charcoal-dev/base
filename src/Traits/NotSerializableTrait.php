<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Traits;

/**
 * A trait to disable all serialization and deserialization methods for classes that use it.
 */
trait NotSerializableTrait
{
    use BlockLegacySerializationTrait;

    final public function __serialize(): never
    {
        throw new \BadMethodCallException(static::class . " instance cannot be serialized");
    }

    final public function __unserialize(array $data): never
    {
        throw new \BadMethodCallException(static::class . " instance cannot be unserialized");
    }
}