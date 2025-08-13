<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Contracts\Vectors;

use Charcoal\Base\Vectors\StringVector;

/**
 * StringVectorProviderInterface
 */
interface StringVectorProviderInterface
{
    /**
     * @return StringVector
     */
    public function toStringVector(): StringVector;
}