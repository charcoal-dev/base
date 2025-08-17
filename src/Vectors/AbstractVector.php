<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Vectors;

/**
 * Abstract base class representing a vector-like structure.
 * @template T of mixed
 */
abstract class AbstractVector implements \IteratorAggregate, \Countable
{
    /** @var array<int,T> */
    protected array $values;

    /**
     * @param array<int,T>|null $vector
     */
    protected function __construct(?array $vector = null)
    {
        $this->values = $vector ?? [];
    }

    final public function count(): int
    {
        return count($this->values);
    }

    /**
     * @return \Traversable<int,T>
     */
    final public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->values);
    }

    /**
     * @return array<int,T>
     */
    final public function getArray(): array
    {
        return $this->values;
    }
}