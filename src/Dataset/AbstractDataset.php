<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Dataset;

use Charcoal\Base\Exceptions\WrappedException;
use Charcoal\Contracts\Charsets\Charset;
use Charcoal\Contracts\Errors\ExceptionAction;

/**
 * Represents an abstract dataset encapsulating a collection of key-value pairs
 * with support for iteration and counting.
 * @template Stored of object
 * @template Value of mixed
 * @implements \IteratorAggregate<string,Stored>
 */
abstract class AbstractDataset implements \IteratorAggregate, \Countable
{
    /** @var array<string,Stored> $dataset */
    protected array $dataset = [];

    /**
     * @param Charset $charset
     * @param BatchEnvelope|null $seed
     * @throws WrappedException
     */
    public function __construct(
        public readonly Charset $charset,
        ?BatchEnvelope          $seed = null,
    )
    {
        if ($seed) {
            $this->storeFromBatchEnvelope($seed);
        }
    }

    /**
     * @param BatchEnvelope $batch
     * @return int
     * @throws WrappedException
     * @api
     */
    protected function storeFromBatchEnvelope(BatchEnvelope $batch): int
    {
        if (!$batch->items) {
            return 0;
        }

        $stored = 0;
        foreach ($batch->items as $key => $value) {
            try {
                $this->storeEntry($key, $value);
                $stored++;
            } catch (\Throwable $t) {
                if ($batch->onError === ExceptionAction::Throw) {
                    throw new WrappedException($t, static::class . " encountered " . $t::class .
                        " during store fn from batch envelope");
                }

                if ($batch->onError === ExceptionAction::Log && $batch->errorLogger) {
                    ($batch->errorLogger)($key, $t);
                }
            }
        }

        return $stored;
    }

    /**
     * @param string $key
     * @return string
     */
    protected function normalizeAccessKey(string $key): string
    {
        return $this->charset === Charset::ASCII ? strtolower(trim($key)) :
            mb_strtolower(trim($key), $this->charset->value);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     * @api
     */
    protected function storeEntry(string $key, mixed $value): static
    {
        $this->dataset[$this->normalizeAccessKey($key)] = new KeyValue($key, $value);
        return $this;
    }

    /**
     * @param string $key
     * @return KeyValue|null
     * @api
     */
    protected function getEntry(string $key): ?KeyValue
    {
        return $this->dataset[$this->normalizeAccessKey($key)] ?? null;
    }

    /**
     * @param string $key
     * @return $this
     * @api
     */
    protected function deleteEntry(string $key): static
    {
        unset($this->dataset[$this->normalizeAccessKey($key)]);
        return $this;
    }

    /**
     * @param string $key
     * @return bool
     */
    final public function has(string $key): bool
    {
        return isset($this->dataset[$this->normalizeAccessKey($key)]);
    }

    /**
     * @return int
     * @api
     */
    final public function count(): int
    {
        return count($this->dataset);
    }

    /**
     * @return array
     */
    final public function inspect(): array
    {
        return array_keys($this->dataset);
    }

    /**
     * @return void
     * @api
     */
    final protected function flushEntries(): void
    {
        $this->dataset = [];
    }

    /**
     * @return array<string,Value>
     */
    final public function getArray(): array
    {
        $data = [];
        foreach ($this->dataset as $prop) {
            $data[$prop->key] = $prop->value;
        }

        return $data;
    }

    /**
     * @return array<string,Stored>
     */
    final public function getDataset(): array
    {
        return $this->dataset;
    }

    /**
     * @return \Traversable<string,Stored>
     */
    final public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->dataset);
    }
}