<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Charsets;

use Charcoal\Base\Contracts\Charsets\UnicodeLanguageRangeInterface;

/**
 * Enum Unicode represents a set of predefined Unicode language options.
 * It defines constants for different language categories and provides methods
 * for retrieving associated Unicode ranges.
 */
enum Unicode implements UnicodeLanguageRangeInterface
{
    public const array LOCALES_CYRILLIC = [Locale::RU, Locale::UK, Locale::BG,
        Locale::SR, Locale::MK, Locale::BE];

    public const array LOCALES_LATIN = [Locale::EN, Locale::FR, Locale::DE, Locale::ES, Locale::IT,
        Locale::PT, Locale::NL, Locale::SV, Locale::NO, Locale::DA, Locale::FI, Locale::PL,
        Locale::CS, Locale::SK, Locale::HU, Locale::RO];

    case RUSSIAN;
    case CYRILLIC;
    case LATIN1_SUPPLEMENT;
    case LATIN_EXTENDED_A;

    /**
     * @return string[]
     */
    public function getUnicodeRange(): array
    {
        return match ($this) {
            self::CYRILLIC,
            self::RUSSIAN => ["\x{0400}-\x{045F}", "\x{048A}-\x{052F}"],
            self::LATIN1_SUPPLEMENT => ["\x{00C0}-\x{00FF}"],
            self::LATIN_EXTENDED_A => ["\x{0100}-\x{017F}"],
        };
    }

    /**
     * @return array[]
     */
    public function getLocales(): array
    {
        return ["Cyrillic" => self::LOCALES_CYRILLIC,
            "Latin" => self::LOCALES_LATIN];
    }

    /**
     * @param Locale|null $locale
     * @return self|null
     */
    public static function fromLocale(?Locale $locale): ?self
    {
        if (!$locale) {
            return null;
        }

        return match (true) {
            in_array($locale, self::LOCALES_CYRILLIC, true) => self::CYRILLIC,
            in_array($locale, self::LOCALES_LATIN, true) => self::LATIN_EXTENDED_A,
            default => null,
        };
    }
}