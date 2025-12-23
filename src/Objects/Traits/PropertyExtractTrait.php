<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Objects\Traits;

/**
 * Extracts properties from an object.
 */
trait PropertyExtractTrait
{
    /**
     * @param string ...$props
     * @return array
     */
    public function extract(string ...$props): array
    {
        $dataSet = [];
        foreach ($props as $prop) {
            $dataSet[$prop] = $this->$prop ?? null;
        }

        return $dataSet;
    }
}