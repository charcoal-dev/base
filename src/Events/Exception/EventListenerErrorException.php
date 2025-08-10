<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

namespace Charcoal\Base\Events\Exception;

use Charcoal\Base\Events\AbstractEvent;
use Charcoal\Base\Exceptions\WrappedException;

/**
 * EventListenerErrorException
 * Represents an exception that encapsulates an error occurring in an event listener.
 */
class EventListenerErrorException extends WrappedException
{
    public function __construct(
        \Throwable                    $previous,
        public readonly AbstractEvent $event,
        public readonly string        $listenerId,
        public readonly array         $args,
    )
    {
        parent::__construct($previous);
    }
}