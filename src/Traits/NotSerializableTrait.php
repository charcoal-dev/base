<?php
declare(strict_types=1);

/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

namespace Charcoal\Base\Traits;

/**
 * A trait to disable all serialization and deserialization methods for classes that use it.
 * Prevents any attempts to serialize, unserialize, export, or restore an instance of the class.
 * Each method will throw a BadMethodCallException when invoked.
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