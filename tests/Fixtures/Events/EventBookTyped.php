<?php
declare(strict_types=1);

/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

namespace Charcoal\Base\Tests\Fixtures\Events;

use Charcoal\Base\Events\AbstractEventsRegistry;
use Charcoal\Base\Events\AbstractEvent;

class EventBookTyped extends AbstractEventsRegistry
{
    public readonly AbstractEvent $event1;
    public readonly AbstractEvent $event2;
    public readonly AbstractEvent $event3;

    public function __construct()
    {
        $this->event1 = $this->getEvent("event1");
        $this->event2 = $this->getEvent("event2");
        $this->event3 = $this->getEvent("event3");;
    }
}