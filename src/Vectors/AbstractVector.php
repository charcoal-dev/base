<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Vectors;

/**
 * Abstract base class representing a vector-like structure.
 */
abstract class AbstractVector implements \IteratorAggregate, \Countable
{
    protected array $values;

    protected function __construct(?array $vector = null)
    {
        $this->values = $vector ?? [];
    }

    final public function count(): int
    {
        return count($this->values);
    }

    final public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->values);
    }

    final public function getArray(): array
    {
        return $this->values;
    }
}