<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Enums;

/**
 * This enum provides a set of predefined fetch origins.
 */
enum FetchOrigin: string
{
    case CACHE = "cache";
    case RUNTIME = "runtime";
    case DATABASE = "db";
}