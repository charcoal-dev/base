<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Strings;

/**
 * Enumeration representing various case styles for string transformation.
 */
enum CaseStyle
{
    case RAW;
    case SNAKE_CASE;
    case KEBAB_CASE;
    case CAMEL_CASE;
    case PASCAL_CASE;

    /**
     * @param string $input
     * @param CaseStyle $to
     * @return string
     */
    public function to(string $input, CaseStyle $to): string
    {
        return $to->from($input, $this);
    }

    /**
     * @param string $input
     * @param CaseStyle $from
     * @return string
     */
    public function from(string $input, CaseStyle $from = self::RAW): string
    {
        if ($this === $from) {
            return $input;
        }

        return match ($this) {
            self::RAW => $input,
            self::SNAKE_CASE => $this->toDelimiterCase($input, "_", $from),
            self::KEBAB_CASE => $this->toDelimiterCase($input, "-", $from),
            self::PASCAL_CASE,
            self::CAMEL_CASE => $this->toStyledCase($input, $from),
        };
    }

    /**
     * @param string $input
     * @param CaseStyle $from
     * @return string
     */
    private function toStyledCase(string $input, CaseStyle $from = self::RAW): string
    {
        $result = match ($from) {
            self::SNAKE_CASE,
            self::KEBAB_CASE => [CaseStyleHelper::pascalCaseFromRaw($input), true],
            self::RAW,
            self::CAMEL_CASE,
            self::PASCAL_CASE => [CaseStyleHelper::camelCaseFromStyled($input), false],
        };

        $requireUc = match ($this) {
            default => false,
            self::PASCAL_CASE => true
        };

        return ($requireUc !== $result[1]) ?
            ($requireUc ? ucfirst($result[0]) : lcfirst($result[0])) :
            $result[0];
    }

    /**
     * @param string $input
     * @param string $glue
     * @param CaseStyle $from
     * @return string
     */
    private function toDelimiterCase(string $input, string $glue, CaseStyle $from): string
    {
        return match ($from) {
            self::RAW,
            self::SNAKE_CASE,
            self::KEBAB_CASE => CaseStyleHelper::delimiterCaseFromRaw($input, $glue),
            self::CAMEL_CASE,
            self::PASCAL_CASE => CaseStyleHelper::delimiterCaseFromStyled($input, $glue),
        };
    }
}