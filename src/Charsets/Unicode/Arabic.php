<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Charsets\Unicode;

use Charcoal\Base\Charsets\Locale;
use Charcoal\Base\Contracts\Charsets\UnicodeLanguageRangeInterface;

/**
 * Modern Arabic Unicode range and related constants for use in text processing.
 */
enum Arabic implements UnicodeLanguageRangeInterface
{
    public const array LOCALES = [Locale::AR, Locale::UR, Locale::FA, Locale::PS,
        Locale::SD, Locale::KS, Locale::UG, Locale::KU];

    case MODERN;
    case FULL;

    /**
     * @return Locale[]
     */
    public function getLocales(): array
    {
        return self::LOCALES;
    }

    /**
     * @param Locale|null $locale
     * @return self|null
     */
    public static function fromLocale(?Locale $locale): ?self
    {
        if (!$locale || !in_array($locale, self::LOCALES, true)) {
            return null;
        }

        return match ($locale) {
            Locale::AR, Locale::UR, Locale::FA => self::MODERN,
            default => self::FULL,
        };
    }

    /**
     * @return string[]
     */
    public function getUnicodeRange(): array
    {
        return match ($this) {
            self::MODERN => [
                "\x{0621}-\x{063A}",
                "\x{0641}-\x{064A}",
                "\x{064B}-\x{065F}",
                "\x{0640}",
                "\x{0660}-\x{0669}",
                "\x{06F0}-\x{06F9}",
                "\x{067E}", "\x{0686}", "\x{0698}", "\x{06A9}", "\x{06AF}",
                "\x{06BA}", "\x{06BE}", "\x{06C1}", "\x{06CC}", "\x{06D2}", "\x{06D3}",
                "\x{060C}", "\x{061B}", "\x{061F}"
            ],
            self::FULL => [
                "\x{0600}-\x{0604}", "\x{0606}-\x{060B}", "\x{060D}-\x{061A}", "\x{061E}",
                "\x{0620}-\x{063F}", "\x{0641}-\x{064A}", "\x{0656}-\x{066F}",
                "\x{0671}-\x{06DC}", "\x{06DE}-\x{06FF}", "\x{0750}-\x{077F}",
                "\x{08A0}-\x{08B4}", "\x{08B6}-\x{08BD}", "\x{08D4}-\x{08E1}", "\x{08E3}-\x{08FF}",
                "\x{FB1D}-\x{FB36}", "\x{FB38}-\x{FB3C}", "\x{FB3E}",
                "\x{FB40}-\x{FB41}", "\x{FB43}-\x{FB44}", "\x{FB46}-\x{FBC1}",
                "\x{FBD3}-\x{FD3D}", "\x{FD50}-\x{FD8F}", "\x{FD92}-\x{FDC7}",
                "\x{FDF0}-\x{FDFD}", "\x{FE70}-\x{FE74}", "\x{FE76}-\x{FEFC}",
                "\x{10E60}-\x{10E7E}", "\x{1EE00}-\x{1EE03}", "\x{1EE05}-\x{1EE1F}",
                "\x{1EE21}-\x{1EE22}", "\x{1EE24}", "\x{1EE27}", "\x{1EE29}-\x{1EE32}",
                "\x{1EE34}-\x{1EE37}", "\x{1EE39}", "\x{1EE3B}", "\x{1EE42}", "\x{1EE47}",
                "\x{1EE49}", "\x{1EE4B}", "\x{1EE4D}-\x{1EE4F}", "\x{1EE51}-\x{1EE52}",
                "\x{1EE54}", "\x{1EE57}", "\x{1EE59}", "\x{1EE5B}", "\x{1EE5D}", "\x{1EE5F}",
                "\x{1EE61}-\x{1EE62}", "\x{1EE64}", "\x{1EE67}-\x{1EE6A}",
                "\x{1EE6C}-\x{1EE72}", "\x{1EE74}-\x{1EE77}", "\x{1EE79}-\x{1EE7C}",
                "\x{1EE7E}", "\x{1EE80}-\x{1EE89}", "\x{1EE8B}-\x{1EE9B}",
                "\x{1EEA1}-\x{1EEA3}", "\x{1EEA5}-\x{1EEA9}", "\x{1EEAB}-\x{1EEBB}",
                "\x{1EEF0}-\x{1EEF1}",
                "\x{064B}-\x{0655}",
                "\x{06F0}-\x{06F9}",
                "\x{060C}", "\x{061B}", "\x{061F}",
                "\x{0640}",
                "\x{061C}", "\x{200C}", "\x{200D}",
            ]
        };
    }
}