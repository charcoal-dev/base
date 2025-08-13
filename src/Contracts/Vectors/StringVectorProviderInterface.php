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
interface StringVectorProviderInterface extends StringVectorInterface
{
    public function filterUnique(): static;

    public function toStringVector(): StringVector;
}