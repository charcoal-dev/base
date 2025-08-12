<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Charsets\Sanitizer\Modifiers;

use Charcoal\Base\Contracts\Charsets\SanitizerModifierInterface;
use Charcoal\Base\Enums\Charset;

/**
 * ChangeCase Modifier
 */
enum ChangeCase implements SanitizerModifierInterface
{
    case Uppercase;
    case Lowercase;
    case Titlecase;

    /**
     * @param string $value
     * @param Charset $charset
     * @return string
     */
    public function apply(string $value, Charset $charset): string
    {
        return match ($this) {
            self::Uppercase => match ($charset) {
                Charset::ASCII => strtoupper($value),
                Charset::UTF8 => mb_strtoupper($value, "UTF-8"),
            },
            self::Lowercase => match ($charset) {
                Charset::ASCII => strtolower($value),
                Charset::UTF8 => mb_strtolower($value, "UTF-8"),
            },
            self::Titlecase => match ($charset) {
                Charset::ASCII => ucfirst($value),
                Charset::UTF8 => mb_convert_case($value, MB_CASE_TITLE, "UTF-8"),
            },
        };
    }
}