<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Charsets;

/**
 * Locale
 */
enum Locale: string
{
    // Arabic script languages
    case AR = "Arabic";
    case UR = "Urdu";
    case FA = "Persian";
    case PS = "Pashto";
    case SD = "Sindhi";
    case KS = "Kashmiri";
    case UG = "Uyghur";
    case KU = "Kurdish";

    // Cyrillic script languages
    case RU = "Russian";
    case UK = "Ukrainian";
    case BG = "Bulgarian";
    case SR = "Serbian";
    case MK = "Macedonian";
    case BE = "Belarusian";

    // Latin script (basic Western/Eastern)
    case EN = "English";
    case FR = "French";
    case DE = "German";
    case ES = "Spanish";
    case IT = "Italian";
    case PT = "Portuguese";
    case NL = "Dutch";
    case SV = "Swedish";
    case NO = "Norwegian";
    case DA = "Danish";
    case FI = "Finnish";
    case PL = "Polish";
    case CS = "Czech";
    case SK = "Slovak";
    case HU = "Hungarian";
    case RO = "Romanian";

    // Southeast Asian scripts
    case TH = "Thai";
    case LO = "Lao";
    case MY = "Burmese";
    case KM = "Khmer";

    // East Asian scripts
    case ZH = "Chinese";
    case JA = "Japanese";
    case KO = "Korean";
}