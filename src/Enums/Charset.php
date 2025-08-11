<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Enums;

/**
 * This enum provides constants for common character encodings, such as ASCII and UTF-8.
 */
enum Charset: string
{
    case ASCII = "ASCII";
    case UTF8 = "UTF-8";
}