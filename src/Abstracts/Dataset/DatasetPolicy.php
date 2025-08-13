<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Abstracts\Dataset;

use Charcoal\Base\Enums\ValidationState;

/**
 * Represents a policy for accessing and manipulating datasets.
 */
readonly class DatasetPolicy
{
    /**
     * @param DatasetStorageMode $mode Storage mode for the dataset
     * @param ValidationState $accessKeyTrust Validation checks on keys provided to access (has/get/delete) methods
     * @param ValidationState $setterKeyTrust Validation checks on keys provided to setter methods
     * @param ValidationState $valueTrust Validation checks on values provided to setter methods
     */
    public function __construct(
        public DatasetStorageMode $mode = DatasetStorageMode::ENTRY_OBJECTS,
        public ValidationState    $accessKeyTrust = ValidationState::RAW,
        public ValidationState    $setterKeyTrust = ValidationState::RAW,
        public ValidationState    $valueTrust = ValidationState::RAW,
    )
    {
        if (!$this->setterKeyTrust->meets($this->accessKeyTrust)) {
            throw new \InvalidArgumentException("Setter key trust cannot be less restrictive than access key trust");
        }
    }
}