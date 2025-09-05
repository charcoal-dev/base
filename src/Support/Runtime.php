<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Support;

/**
 * A helper class for runtime assertions.
 */
abstract readonly class Runtime
{
    /**
     * @param bool $condition
     * @param string $message
     * @return void
     */
    public static function assert(bool $condition, string $message = ""): void
    {
        if (!$condition) {
            throw new \AssertionError($message ?: "Assertion failed");
        }
    }

    /**
     * @return int
     */
    public static function getPid(): int
    {
        return getmypid();
    }
}