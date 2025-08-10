<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Enums;

/**
 * This enum provides a set of predefined actions to handle exceptions,
 * allowing for consistent exception management choices.
 */
enum ExceptionAction
{
    case Throw;
    case Ignore;
    case Log;
}