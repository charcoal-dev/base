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
    public function __construct(string ...$values)
    {
        parent::__construct();
        $this->append(...$values);
    }

    public function append(string ...$values): static
    {
        foreach ($values as $value) {
            $value = trim($value);
            if ($value !== "") {
                $this->values[] = $value;
            }
        }

        return $this;
    }

    public function filterUnique(): static
    {
        $this->values = array_values(array_unique($this->values, SORT_STRING));
        return $this;
    }
}