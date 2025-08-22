<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Support\Callbacks;

use Charcoal\Base\Contracts\Callbacks\SerializableCallback;

/**
 * This class allows storing a callable (static method or function) along
 * with optional arguments, which can be invoked at a later time. The object
 * can also be serialized and deserialized, maintaining its functionality.
 */
readonly class StaticCallback implements SerializableCallback
{
    private string|array $callback;
    private ?array $args;

    /**
     * @param string|array $callback
     * @param bool|string|int|null ...$args
     * @return self
     */
    public static function getSerializable(string|array $callback, bool|string|int|null ...$args): self
    {
        return new self($callback, ...$args);
    }

    /**
     * Constructor for initializing the callback and its arguments.
     */
    private function __construct(string|array $callback, bool|string|int|null ...$args)
    {
        $callback = is_string($callback) && str_contains($callback, "::") ?
            explode("::", $callback) : $callback;

        $this->callback = match (true) {
            is_string($callback),
            (is_array($callback) && count($callback) === 2 &&
                is_string($callback[0]) && is_string($callback[1] ?? null)) => $callback,
            default => throw new \InvalidArgumentException("Invalid callback"),
        };

        if (!is_callable($this->callback)) {
            throw new \InvalidArgumentException("Callback is not callable: " .
                implode("::", $this->callback));
        }

        $this->args = $args ?: null;
    }

    /**
     * Invokes the callback with the given arguments.
     */
    public function invoke(mixed ...$args): string
    {
        if (!is_callable($this->callback)) {
            throw new \RuntimeException(self::class . ': method ' . $this->callback[1] . ' of class ' .
                $this->callback[0] . ' is no longer callable');
        }

        $args = $this->args ? [...$this->args, ...$args] : $args;
        return ($this->callback)(...$args);
    }

    /**
     * @param mixed ...$args
     * @return mixed
     */
    public function __invoke(mixed ...$args): mixed
    {
        return $this->invoke(...$args);
    }

    /**
     * @return array<string|array<string,string>, mixed>
     */
    public function __serialize(): array
    {
        return ["callback" => $this->callback, "args" => $this->args];
    }

    /**
     * Restores the state of the object from the given serialized data.
     */
    public function __unserialize(array $data): void
    {
        $this->callback = $data["callback"];
        $this->args = $data["args"];
    }
}