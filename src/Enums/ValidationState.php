<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Enums;

/**
 * An enumeration representing the various states of validation for arbitrary data.
 */
enum ValidationState: int
{
    case RAW = 0;
    case SANITIZED = 1;
    case NORMALIZED = 2;
    case VALIDATED = 3;
    case TRUSTED = 4;
}