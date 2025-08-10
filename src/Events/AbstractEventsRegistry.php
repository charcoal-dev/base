<?php
declare(strict_types=1);

/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

namespace Charcoal\Base\Events;

use Charcoal\Base\Concerns\RequiresNormalizedRegistryKeys;
use Charcoal\Base\Enums\ExceptionAction;
use Charcoal\Base\Traits\ControlledSerializableTrait;

/**
 * Represents an abstract registry for managing events.
 * Uses the functionality provided by the ObjectsRegistryTrait
 * to handle the registration and management of event-related objects.
 */
abstract class AbstractEventsRegistry
{
    public ExceptionAction $onListenerError = ExceptionAction::Throw;
    private array $events = [];

    use RequiresNormalizedRegistryKeys;
    use ControlledSerializableTrait;

    /**
     * @param AbstractEvent|string $event
     * @return AbstractEvent
     */
    protected function getEvent(AbstractEvent|string $event): AbstractEvent
    {
        $name = $event instanceof AbstractEvent ?
            $event->name : $this->normalizeRegistryKey($event);

        if (isset($this->events[$name])) {
            return $this->events[$name];
        }

        return $this->events[$name] = new AbstractEvent($this, $name);
    }

    /**
     * @param AbstractEvent|string $event
     * @return bool
     */
    public function hasEvent(AbstractEvent|string $event): bool
    {
        $name = $event instanceof AbstractEvent ?
            $event->name : $this->normalizeRegistryKey($event);

        return isset($this->events[$name]);
    }

    /**
     * @param AbstractEvent|string $event
     * @return void
     */
    protected function clearEvent(AbstractEvent|string $event): void
    {
        $name = $event instanceof AbstractEvent ?
            $event->name : $this->normalizeRegistryKey($event);

        unset($this->events[$name]);
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return count($this->events);
    }

    /**
     * @return array
     */
    protected function collectSerializableData(): array
    {
        return ["events" => $this->events,
            "onListenerError" => $this->onListenerError];
    }

    /**
     * @param array $data
     * @return void
     */
    public function __unserialize(array $data): void
    {
        $this->events = $data["events"];
        $this->onListenerError = $data["onListenerError"];
    }

    /**
     * @param EventListenerErrorException $context
     * @return void
     */
    public function onExceptionCaught(EventListenerErrorException $context): void
    {
    }

    /**
     * @param string $key
     * @return string
     */
    protected function normalizeRegistryKey(string $key): string
    {
        return $key;
    }
}