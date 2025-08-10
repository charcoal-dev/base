<?php
declare(strict_types=1);

/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

namespace Charcoal\Base\Events;

use Charcoal\Base\Exception\WrappedException;

/**
 * EventListenerErrorException
 * Represents an exception that encapsulates an error occurring in an event listener.
 */
class EventListenerErrorException extends WrappedException
{
    public function __construct(
        \Throwable                $previous,
        public readonly BaseEvent $event,
        public readonly string    $listenerId,
        public readonly array     $args,
    )
    {
        parent::__construct($previous);
    }
}