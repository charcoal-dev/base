<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Abstracts;

use Charcoal\Base\Abstracts\Dataset\BatchEnvelope;
use Charcoal\Base\Abstracts\Dataset\KeyValueTrust;
use Charcoal\Base\Enums\Charset;
use Charcoal\Base\Enums\ValidationState;
use Charcoal\Base\Exceptions\WrappedException;

/**
 * Abstract class for managing datasets with validation and normalization capabilities.
 * This class provides mechanisms to validate, normalize, and store data entries,
 * ensuring compliance with a defined dataset policy.
 * @template Value of mixed
 * @extends AbstractDataset<KeyValueTrust,Value>
 */
abstract class AbstractDatasetValidated extends AbstractDataset
{
    /**
     * @param Charset $charset
     * @param ValidationState $accessKeyTrust
     * @param ValidationState $setterKeyTrust
     * @param ValidationState $valueTrust
     * @param BatchEnvelope|null $seed
     * @throws WrappedException
     */
    public function __construct(
        public readonly Charset $charset,
        public ValidationState  $accessKeyTrust = ValidationState::RAW,
        public ValidationState  $setterKeyTrust = ValidationState::RAW,
        public ValidationState  $valueTrust = ValidationState::RAW,
        ?BatchEnvelope          $seed = null
    )
    {
        parent::__construct($charset, $seed);
    }

    abstract protected function validateEntryKey(string $key): string;

    abstract protected function validateEntryValue(mixed $value, string $key): mixed;

    /**
     * @param string $key
     * @return string
     */
    final protected function policyValidateEntryKey(string $key): string
    {
        return $this->setterKeyTrust->meets(ValidationState::VALIDATED) ?
            $key : $this->validateEntryKey($key);
    }

    /**
     * @param string $key
     * @return string
     */
    final protected function policyValidateAccessKey(string $key): string
    {
        return $this->accessKeyTrust->meets(ValidationState::VALIDATED) ?
            $key : $this->validateEntryKey($key);
    }

    /**
     * @param mixed $value
     * @param string $validatedKey
     * @return mixed
     */
    final protected function policyValidateEntryValue(mixed $value, string $validatedKey): mixed
    {
        return $this->valueTrust->meets(ValidationState::VALIDATED) ?
            $value : $this->validateEntryValue($value, $validatedKey);
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
        $this->dataset[$accessKey] = new KeyValueTrust($key, $value, ValidationState::VALIDATED);
        return $this;
    }

    /**
     * @param string $key
     * @return KeyValueTrust|null
     * @api
     */
    protected function getEntry(string $key): ?KeyValueTrust
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
}