<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Support\Data;

use Charcoal\Base\Enums\ValidationState;

/**
 * This class is used to encapsulate a single data item with its associated metadata, allowing
 * for validation and storage of key-value pairs.
 */
class CheckedKeyValue
{
    public function __construct(
        public readonly string    $key,
        public readonly mixed     $value,
        protected ValidationState $state,
    )
    {
    }

    public function changeState(ValidationState $state): void
    {
        $this->state = $state;
    }
}