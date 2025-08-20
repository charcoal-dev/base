<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Abstracts\Dataset;

/**
 * This class is designed to store a key of type string and an associated value
 * of any type. Both properties are publicly accessible but read-only, ensuring
 * that once an instance is created, its data cannot be modified.
 * @template T of mixed
 */
readonly class KeyValue
{
    public function __construct(
        public string $key,
        public mixed  $value,
    )
    {
    }
}