<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Abstracts\Dataset;

use Charcoal\Base\Enums\ExceptionAction;
use Charcoal\Base\Enums\ValidationState;
use Charcoal\Base\Support\Data\BatchEnvelope;
use Charcoal\Base\Support\Data\CheckedKeyValue;

/**
 * Abstract class for managing datasets with validation and normalization capabilities.
 * This class provides mechanisms to validate, normalize, and store data entries,
 * ensuring compliance with a defined dataset policy.
 * @template T of mixed|CheckedKeyValue
 */
abstract class ValidatingDataset implements \IteratorAggregate, \Countable
{
    /** @var array<string,T> $dataset */
    private array $dataset = [];

    public function __construct(
        public readonly DatasetPolicy $policy,
        ?BatchEnvelope                $initialData = null
    )
    {
        if ($initialData && $initialData->items) {
            foreach ($initialData->items as $key => $value) {
                try {
                    $this->storeEntry($key, $value);
                } catch (\Throwable $t) {
                    if ($initialData->onError === ExceptionAction::Throw) {
                        /** @noinspection PhpUnhandledExceptionInspection */
                        throw $t;
                    }

                    if ($initialData->onError === ExceptionAction::Log && $initialData->errorLogger) {
                        ($initialData->errorLogger)($key, $t);
                    }
                }
            }
        }
    }

    abstract protected function validateEntryKey(string $key): string;

    abstract protected function validateEntryValue(mixed $value, string $key): mixed;

    /**
     * @param string $key
     * @return string
     */
    final protected function policyValidateEntryKey(string $key): string
    {
        return $this->policy->setterKeyTrust->meets(ValidationState::VALIDATED) ?
            $key : $this->validateEntryKey($key);
    }

    /**
     * @param string $key
     * @return string
     */
    final protected function policyValidateAccessKey(string $key): string
    {
        return $this->policy->accessKeyTrust->meets(ValidationState::VALIDATED) ?
            $key : $this->validateEntryKey($key);
    }

    /**
     * @param mixed $value
     * @param string $validatedKey
     * @return mixed
     */
    final protected function policyValidateEntryValue(mixed $value, string $validatedKey): mixed
    {
        return $this->policy->valueTrust->meets(ValidationState::VALIDATED) ?
            $value : $this->validateEntryValue($value, $validatedKey);
    }

    /**
     * @param string $key
     * @return string
     */
    protected function normalizeAccessKey(string $key): string
    {
        return strtolower(trim($key));
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     * @api
     */
    protected function storeEntry(string $key, mixed $value): static
    {
        $key = $this->policyValidateEntryKey($key);
        $accessKey = $this->normalizeAccessKey($key);
        $value = $this->policyValidateEntryValue($value, $key);
        $this->dataset[$accessKey] = $this->policy->mode === DatasetStorageMode::ENTRY_OBJECTS ?
            new CheckedKeyValue($key, $value, ValidationState::VALIDATED) : $value;

        return $this;
    }

    /**
     * @param string $key
     * @return T|null
     * @api
     */
    protected function getEntry(string $key): mixed
    {
        $index = $this->normalizeAccessKey($this->policyValidateAccessKey($key));
        return $this->dataset[$index] ?? null;
    }

    /**
     * @param string $key
     * @return $this
     * @api
     */
    protected function deleteEntry(string $key): static
    {
        $index = $this->normalizeAccessKey($this->policyValidateAccessKey($key));
        unset($this->dataset[$index]);
        return $this;
    }

    /**
     * @param string $key
     * @return bool
     */
    final public function has(string $key): bool
    {
        return array_key_exists($this->normalizeAccessKey($this->policyValidateAccessKey($key)),
            $this->dataset);
    }

    /**
     * @return int
     */
    final public function count(): int
    {
        return count($this->dataset);
    }

    /**
     * @return array
     */
    final public function getStoredKeys(): array
    {
        return array_keys($this->dataset);
    }

    /**
     * @return array<string,T>
     */
    final public function getArray(): array
    {
        if ($this->policy->mode === DatasetStorageMode::ENTRY_OBJECTS) {
            $data = [];
            /** @var CheckedKeyValue $prop */
            foreach ($this->dataset as $prop) {
                $data[$prop->key] = $prop->value;
            }

            return $data;
        }

        return $this->dataset;
    }

    /**
     * @return \Traversable<string,T>
     */
    final public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->dataset);
    }
}