<?php
declare(strict_types=1);

/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

namespace Charcoal\Base\Events;

use Charcoal\Base\Enums\ExceptionAction;
use Charcoal\Base\Registry\ObjectsRegistryTrait;
use Charcoal\Base\Traits\ControlledSerializableTrait;

/**
 * Represents an abstract registry for managing events.
 * Uses the functionality provided by the ObjectsRegistryTrait
 * to handle the registration and management of event-related objects.
 */
abstract class AbstractEventsRegistry
{
    public ExceptionAction $onListenerError = ExceptionAction::Throw;

    use ObjectsRegistryTrait;
    use ControlledSerializableTrait;

    /**
     * @param BaseEvent|string $event
     * @return BaseEvent
     */
    protected function getEvent(BaseEvent|string $event): BaseEvent
    {
        $name = $event instanceof BaseEvent ?
            $event->name : $this->normalizeRegistryKey($event);

        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }

        return $this->instances[$name] = new BaseEvent($this, $name);
    }

    /**
     * @param BaseEvent|string $event
     * @return bool
     */
    public function hasEvent(BaseEvent|string $event): bool
    {
        $name = $event instanceof BaseEvent ?
            $event->name : $this->normalizeRegistryKey($event);

        return isset($this->instances[$name]);
    }

    /**
     * @param BaseEvent|string $event
     * @return void
     */
    protected function clearEvent(BaseEvent|string $event): void
    {
        $name = $event instanceof BaseEvent ?
            $event->name : $this->normalizeRegistryKey($event);

        unset($this->instances[$name]);
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return count($this->instances);
    }

    /**
     * @return array
     */
    protected function collectSerializableData(): array
    {
        return ["events" => $this->instances,
            "onListenerError" => $this->onListenerError];
    }

    /**
     * @param array $data
     * @return void
     */
    public function __unserialize(array $data): void
    {
        $this->instances = $data["events"];
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