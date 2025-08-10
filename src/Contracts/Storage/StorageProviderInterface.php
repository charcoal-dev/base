<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Contracts\Storage;

use Charcoal\Base\Enums\StorageType;

/**
 * Defines a contract for storage providers to specify the type of storage they support.
 */
interface StorageProviderInterface
{
    public function storageType(): StorageType;

    public function storageProviderId(): string;
}