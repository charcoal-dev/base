<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Abstracts\Dataset;

use Charcoal\Base\Enums\ValidationState;

/**
 * Represents a key-value pair with an associated trust state.
 * This class is immutable and extends the base KeyValue class.
 * It uses a ValidationState instance to denote its trust level.
 * @template T of mixed
 * @extends KeyValue<T>
 */
readonly class KeyValueTrust extends KeyValue
{
    public function __construct(
        string                 $key,
        mixed                  $value,
        public ValidationState $trust,
    )
    {
        parent::__construct($key, $value);
    }

    /**
     * Modifies the trust state of the object with the provided
     * ValidationState returning a new instance.
     */
    public function changeTrust(ValidationState $trust): static
    {
        return new static($this->key, $this->value, $trust);
    }
}