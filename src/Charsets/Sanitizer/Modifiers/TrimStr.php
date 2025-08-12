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
 * TrimStr Modifier
 */
enum TrimStr implements SanitizerModifierInterface
{
    case Both;
    case Left;
    case Right;

    /**
     * @param string $value
     * @param Charset $charset
     * @return string
     */
    public function apply(string $value, Charset $charset): string
    {
        return match ($charset) {
            Charset::ASCII => match ($this) {
                self::Both => trim($value),
                self::Left => ltrim($value),
                self::Right => rtrim($value),
            },
            Charset::UTF8 => match ($this) {
                self::Both => preg_replace('/^[\p{Z}\p{Cc}\x{FEFF}]+|[\p{Z}\p{Cc}\x{FEFF}]+$/u', '', $value),
                self::Left => preg_replace('/^[\p{Z}\p{Cc}\x{FEFF}]+/u', '', $value),
                self::Right => preg_replace('/[\p{Z}\p{Cc}\x{FEFF}]+$/u', '', $value),
            },
        };
    }
}