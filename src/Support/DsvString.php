<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Support;

use Charcoal\Base\Enums\Charset;
use Charcoal\Base\Vectors\StringTokenVector;

/**
 * Represents a delimited string vector, providing functionality to handle
 * delimited strings, manage case sensitivity, and respect character set constraints.
 */
class DsvString extends StringTokenVector
{
    public function __construct(
        ?string                 $values = null,
        public readonly string  $delimiter = ",",
        public readonly Charset $charset = Charset::ASCII,
        bool                    $changeCase = true,
        bool                    $uniqueTokensOnly = true,
    )
    {
        if (strlen($this->delimiter) !== 1) {
            throw new \InvalidArgumentException("DsvString expects single byte chat for delimiter");
        }

        parent::__construct(changeCase: $changeCase,
            uniqueTokensOnly: $uniqueTokensOnly);

        $this->appendJoined((string)$values);
    }

    public function toString(?string $glue = null): string
    {
        return parent::toString($glue ?? $this->delimiter);
    }

    public function appendJoined(string $values): static
    {
        return $this->append(...explode($this->delimiter, $values));
    }

    protected function normalizeStringValue(string $value): ?string
    {
        $normalized = parent::normalizeStringValue($value);
        if (!$normalized) {
            return null;
        }

        if (str_contains($normalized, $this->delimiter)) {
            throw new \InvalidArgumentException("Delimiter character cannot be used in string");
        }

        return $normalized;
    }

    protected function toLowerCase(string $value): string
    {
        return $this->charset !== Charset::ASCII
            ? mb_strtolower($value, $this->charset->value)
            : strtolower($value);
    }
}