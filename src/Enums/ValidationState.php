<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Enums;

/**
 * An enumeration representing the various states of validation for an arbitrary data.
 */
enum ValidationState: int
{
    case RAW = 0;
    case NORMALIZED = 1;
    case VALIDATED = 2;
    case TRUSTED = 3;
}