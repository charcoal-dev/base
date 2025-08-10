<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Vectors;

/**
 * A vector-like structure that stores strings.
 */
class StringVector extends AbstractVector
{
    /**
     * @param string[] $values
     */
    public function __construct(string ...$values)
    {
        parent::__construct($values);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function append(string $value): static
    {
        if ($value !== "") {
            $this->values[] = $value;
        }

        return $this;
    }

    /**
     * @return static New vector instance with unique values
     */
    public function filterUnique(): static
    {
        return new static(...array_unique($this->values, SORT_STRING));
    }
}