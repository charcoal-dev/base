<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Enums;

/**
 * This enum provides a set of predefined primitive types.
 */
enum PrimitiveType: string
{
    case STRING = "string";
    case INT = "int";
    case FLOAT = "float";
    case BOOL = "bool";
    case ARRAY = "array";
    case OBJECT = "object";
    case NULL = "null";
    case RESOURCE = "resource";

    /**
     * @param mixed $value
     * @return bool
     */
    public function matches(mixed $value): bool
    {
        return match ($this) {
            self::STRING => is_string($value),
            self::INT => is_int($value),
            self::FLOAT => is_float($value),
            self::BOOL => is_bool($value),
            self::ARRAY => is_array($value),
            self::OBJECT => is_object($value),
            self::NULL => $value === null,
            self::RESOURCE => is_resource($value),
        };
    }

    /**
     * @param mixed $value
     * @return self
     */
    public static function matchFrom(mixed $value): self
    {
        return match (true) {
            is_string($value) => self::STRING,
            is_int($value) => self::INT,
            is_float($value) => self::FLOAT,
            is_bool($value) => self::BOOL,
            is_array($value) => self::ARRAY,
            is_object($value) => self::OBJECT,
            $value === null => self::NULL,
            is_resource($value) => self::RESOURCE,
            default => throw new \InvalidArgumentException("Invalid value type: " . gettype($value)),
        };
    }
}