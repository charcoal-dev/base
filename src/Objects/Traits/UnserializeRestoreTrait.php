<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Objects\Traits;

/**
 * Overwrite every property that exists.
 * @see UnserializeBootstrapTrait hydrating properties where property exists AND is not yet set.
 */
trait UnserializeRestoreTrait
{
    public function __unserialize(array $data): void
    {
        foreach ($data as $prop => $value) {
            if (property_exists($this, $prop)) {
                $this->$prop = $value;
            }
        }
    }
}