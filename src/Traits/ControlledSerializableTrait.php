<?php
declare(strict_types=1);

/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

namespace Charcoal\Base\Traits;

/**
 * Provides controlled serialization support for classes by enforcing
 * abstraction of the serializable data and defining dependency requirements
 * for unserialization.
 */
trait ControlledSerializableTrait
{
    use BlockLegacySerializationTrait;

    abstract protected function collectSerializableData(): array;

    final public function __serialize(): array
    {
        return $this->collectSerializableData();
    }

    /**
     * To maintain an indexed array of classnames that will be passed to unserialize "allowed_classes"
     * @return \class-string[]
     */
    public static function unserializeDependencies(): array
    {
        return [static::class];
    }
}