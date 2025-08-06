<?php
declare(strict_types=1);

/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

namespace Charcoal\Base\Exception;

/**
 * A custom exception class that wraps another exception as its previous throwable.
 * This class allows for enhanced exception handling by providing the ability to wrap and rethrow
 * an existing throwable with an optional custom message and code.
 */
class WrappedException extends \Exception
{
    public function __construct(\Throwable $previous, ?string $message = null, int $code = 0)
    {
        parent::__construct($message ?? static::class, $code, $previous);
    }
}