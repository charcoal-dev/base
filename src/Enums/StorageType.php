<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Enums;

/**
 * This enum provides a set of predefined storage types.
 */
enum StorageType: string
{
    case Cache = "cache";
    case Database = "db";
    case Filesystem = "filesystem";
}