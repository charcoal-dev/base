<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Contracts\Vectors;

/**
 * Provides a structure for classes that require implementation
 * of an array vector functionality for strings.
 */
interface StringVectorInterface
{
    /**
     * Returns the vector as an array of strings
     * @return string[]
     */
    public function getArray(): array;

    /**
     * @return $this
     */
    public function filterUnique(): static;
}