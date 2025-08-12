<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Charsets\Sanitizer\Exception;

/**
 * CharsetSanitizerError
 */
enum CharsetSanitizerError: int
{
    case TYPE_ERROR = 1;
    case CHARSET_ERROR = 2;
    case MODIFIER_CALLBACK_TYPE_ERROR = 11;
    case VALIDATOR_CALLBACK_TYPE_ERROR = 12;
    case VALIDATOR_CALLBACK_FAILED = 13;
    case LENGTH_ERROR = 21;
    case LENGTH_UNDERFLOW_ERROR = 22;
    case LENGTH_OVERFLOW_ERROR = 23;
    case REGEXP_MATCH_ERROR = 31;
    case ENUM_ERROR = 32;
}