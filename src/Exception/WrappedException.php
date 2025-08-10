<?php
/*
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Exception;

/**
 * A custom exception class that wraps another exception as its previous throwable.
 */
class WrappedException extends \Exception
{
    /**
     * @param \Throwable $previous
     * @param string|null $message
     * @param int $code
     */
    public function __construct(\Throwable $previous, ?string $message = null, int $code = 0)
    {
        parent::__construct($message ?? static::class, $code, $previous);
    }
}