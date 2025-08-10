<?php
declare(strict_types=1);

/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

namespace Charcoal\Base\Tests\Fixtures\Events;

use Charcoal\Base\Events\AbstractEventsRegistry;
use Charcoal\Base\Events\AbstractEvent;

class EventBookOne extends AbstractEventsRegistry
{
    public function getEvent(AbstractEvent|string $event): AbstractEvent
    {
        return parent::getEvent($event);
    }

    public function on(AbstractEvent|string $event): AbstractEvent
    {
        return $this->getEvent($event);
    }

    public function clear(AbstractEvent|string $event): void
    {
        $this->clearEvent($event);
    }
}