<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Traits;

/**
 * Provides functionality to prevent instances of a class from being serialized, unserialized, or exported.
 * This trait enforces immutability and restricts legacy serialization mechanisms for better control and security.
 */
trait BlockLegacySerializationTrait
{
    final public static function __set_state(array $in): never
    {
        throw new \BadMethodCallException(static::class . " instance cannot be exported");
    }

    final public function __sleep(): never
    {
        throw new \BadMethodCallException(static::class . " instance cannot be serialized");
    }

    final public function __wakeup(): never
    {
        throw new \BadMethodCallException(static::class . " instance cannot be unserialized");
    }

    final public function serialize(): never
    {
        throw new \BadMethodCallException(static::class . " instance cannot be serialized");
    }

    /** @noinspection PhpUnusedParameterInspection */
    final public function unserialize(string $data): never
    {
        throw new \BadMethodCallException(static::class . " instance cannot be unserialized");
    }
}