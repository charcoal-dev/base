<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Contracts\Charsets;

use Charcoal\Base\Charsets\Locale;

/**
 * UnicodeLanguageRangeInterface
 */
interface UnicodeLanguageRangeInterface
{
    /**
     * @return string[]
     */
    public function getUnicodeRange(): array;

    /**
     * @return Locale[]|array<string,Locale[]>
     */
    public function getLocales(): array;

    /**
     * @param Locale|null $locale
     * @return self|null
     */
    public static function fromLocale(?Locale $locale): ?self;
}