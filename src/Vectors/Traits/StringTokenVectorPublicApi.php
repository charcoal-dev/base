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
     * @internal
     */
    public function append(string ...$values): static
    {
        return $this->addTokens(...$values);
    }

    /**
     * @internal
     */
    public function has(string $value): bool
    {
        return $this->hasToken($value);
    }

    /**
     * @internal
     */
    public function delete(string $value): bool
    {
        return $this->deleteToken($value);
    }
}