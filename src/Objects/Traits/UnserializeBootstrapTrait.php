<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Objects\Traits;

/**
 * Set only if the property exists AND is not yet set.
 * @see UnserializeRestoreTrait to override every property that exists.
 */
trait UnserializeBootstrapTrait
{
    public function __unserialize(array $data): void
    {
        foreach ($data as $prop => $value) {
            if (property_exists($this, $prop) && (!isset($this->$prop) && !is_null($value))) {
                $this->$prop = $value;
            }
        }
    }
}