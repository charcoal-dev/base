<?php
declare(strict_types=1);

/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

namespace Charcoal\Base\Enums;

/**
 * This enum provides a set of predefined actions to handle exceptions, allowing for consistent exception management choices.
 * It includes actions such as throwing the exception, ignoring it, or logging it for further analysis.
 */
enum ExceptionAction
{
    case Throw;
    case Ignore;
    case Log;
}