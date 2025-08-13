<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Charsets\Sanitizer\Modifiers;

use Charcoal\Base\Contracts\Charsets\SanitizerModifierInterface;
use Charcoal\Base\Enums\Charset;
use Charcoal\Base\Support\Helpers\StringHelper;

/**
 * CleanSpaces Modifier
 */
enum CleanSpaces implements SanitizerModifierInterface
{
    case All;

    public function apply(string $value, Charset $charset): string
    {
        return match ($this) {
            self::All => StringHelper::cleanSpaces($value),
        };
    }
}