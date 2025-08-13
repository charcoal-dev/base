<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Enums;

use Charcoal\Base\Contracts\Enums\EncodingEnumInterface;

/**
 * Encoding declaration
 */
enum Encoding: int implements EncodingEnumInterface
{
    case None = 0;
    case Base16 = 16;
    case Base32 = 32;
    case Base58 = 58;
    case Base64 = 64;
}