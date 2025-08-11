<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Vectors;

use Charcoal\Base\Vectors\Traits\StringTokenVectorPublicApi;

/**
 * A specialized implementation of StringVector that provides additional
 * mechanisms for maintaining unique lists of strings, normalizing string
 * values, and utility methods for token manipulation.
 */
class StringTokenVector extends AbstractTokenVector
{
    use StringTokenVectorPublicApi;

    /**
     * @param string $glue
     * @return string
     */
    public function toString(string $glue): string
    {
        return $this->joinString($glue);
    }
}