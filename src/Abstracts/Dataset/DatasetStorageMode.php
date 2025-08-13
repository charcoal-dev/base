<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Abstracts\Dataset;

/**
 * Represents the storage mode for datasets.
 */
enum DatasetStorageMode
{
    /** Values stored as DatasetEntry objects, preserving original keys. */
    case ENTRY_OBJECTS;
    /** Keys are normalized, values are stored as-is (no wrapping) */
    case VALUES_ONLY;
}