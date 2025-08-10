<?php
declare(strict_types=1);

/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

namespace Charcoal\Base\Events;

use Charcoal\Base\Enums\ExceptionAction;
use Charcoal\Base\Events\Exception\EventListenerErrorException;
use Charcoal\Base\Traits\ControlledSerializableTrait;

/**
 * Represents an event that allows attaching and managing listeners.
 */
class AbstractEvent
{
    private array $listeners = [];

    use ControlledSerializableTrait;

    /**
     * @param AbstractEventsRegistry $registry
     * @param string $name
     */
    public function __construct(
        public readonly AbstractEventsRegistry $registry,
        public readonly string                 $name,
    )
    {
    }

    /**
     * @return array
     */
    protected function collectSerializableData(): array
    {
        return [
            "registry" => $this->registry,
            "name" => $this->name,
        ];
    }

    /**
     * @param array $data
     * @return void
     */
    public function __unserialize(array $data): void
    {
        $this->registry = $data["registry"];
        $this->name = $data["name"];
        $this->listeners = [];
    }

    /**
     * @return void
     */
    public function purgeListeners(): void
    {
        $this->listeners = [];
    }

    /**
     * @return int
     */
    public function countListeners(): int
    {
        return count($this->listeners);
    }

    /**
     * @param class-string $uniqueId
     * @param callable $callback
     * @return string
     */
    public function listen(callable $callback, string $uniqueId): string
    {
        $this->listeners[$uniqueId] = $callback;
        return $uniqueId;
    }

    /**
     * @param string $listenerId
     * @return void
     */
    public function unsubscribe(string $listenerId): void
    {
        unset($this->listeners[$listenerId]);
    }

    /**
     * @param array $args
     * @param ExceptionAction|null $onListenerError
     * @return int
     * @throws EventListenerErrorException
     */
    public function trigger(array $args, ?ExceptionAction $onListenerError = null): int
    {
        if (!$this->listeners) {
            return 0;
        }

        $onListenerError = $onListenerError ?? $this->registry->onListenerError;
        $args[] = $this;
        $count = 0;
        foreach ($this->listeners as $listenerId => $listenerFn) {
            try {
                call_user_func_array($listenerFn, $args);
            } catch (\Throwable $t) {
                $error = new EventListenerErrorException($t, $this, $listenerId, $args);
                if ($onListenerError === ExceptionAction::Throw) {
                    throw $error;
                }

                $this->registry->onExceptionCaught($error);
            }

            $count++;
        }

        return $count;
    }
}