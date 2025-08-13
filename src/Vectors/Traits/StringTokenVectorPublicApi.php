<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Vectors\Traits;

/**
 * @internal
 */
trait StringTokenVectorPublicApi
{
    /**
     * @param string ...$values
     * @return $this
     */
    public function append(string ...$values): static
    {
        return $this->addTokens(...$values);
    }

    /**
     * @param string $value
     * @return bool
     */
    public function has(string $value): bool
    {
        return $this->hasToken($value);
    }

    /**
     * @param string $value
     * @return bool
     */
    public function delete(string $value): bool
    {
        return $this->deleteToken($value);
    }
}