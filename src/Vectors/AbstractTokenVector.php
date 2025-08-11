<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Vectors;

use Charcoal\Base\Contracts\Vectors\StringVectorProviderInterface;

/**
 * AbstractTokenVector is an abstract class that manages a collection of string tokens,
 * providing functionality to normalize, append, check, delete, and filter unique string tokens.
 * The behavior of token normalization and uniqueness is configurable through public properties.
 */
class AbstractTokenVector extends AbstractVector implements StringVectorProviderInterface
{
    public function __construct(
        public readonly bool $changeCase = true,
        public readonly bool $uniqueTokensOnly = true,
    )
    {
        parent::__construct();
    }

    protected function joinString(string $glue): string
    {
        if (strlen($glue) !== 1) {
            throw new \InvalidArgumentException("Invalid glue byte to join StringTokenVector");
        }

        return implode($glue, $this->values);
    }

    protected function addTokens(string ...$values): static
    {
        $added = 0;
        foreach ($values as $value) {
            $normalized = $this->normalizeStringValue($value);
            if ($normalized) {
                $this->values[] = $normalized;
                $added++;
            }
        }

        return $this->uniqueTokensOnly && $added > 0 ?
            $this->filterUnique() : $this;
    }

    protected function hasToken(string $token): bool
    {
        $token = trim($token);
        if ($token === "") {
            return false;
        }

        if ($this->changeCase) {
            return in_array($this->toLowerCase($token), $this->values, true);
        }

        $token = $this->toLowerCase($token);
        foreach ($this->values as $value) {
            if ($this->toLowerCase($value) === $token) {
                return true;
            }
        }

        return false;
    }

    protected function deleteToken(string $token): bool
    {
        $token = trim($token);
        if ($token === "") {
            return false;
        }

        $deleted = false;
        $token = $this->changeCase ? $this->toLowerCase($token) : $token;
        foreach ($this->values as $index => $value) {
            $value = $this->changeCase ? $this->toLowerCase($value) : $value;
            if ($value === $token) {
                unset($this->values[$index]);
                $deleted = true;
            }
        }

        if ($deleted) {
            $this->values = array_values($this->values);
        }

        return $deleted;
    }

    public function filterUnique(): static
    {
        if ($this->changeCase) {
            $this->values = array_values(array_unique($this->values, SORT_STRING));
            return $this;
        }

        $seen = [];
        $result = [];
        foreach ($this->values as $value) {
            $lowercase = $this->toLowerCase($value);
            if (!isset($seen[$lowercase])) {
                $seen[$lowercase] = true;
                $result[] = $value;
            }
        }

        $this->values = $result;
        return $this;
    }

    protected function normalizeStringValue(string $value): ?string
    {
        $value = trim($value);
        if ($value === "") {
            return null;
        }

        return $this->changeCase ? $this->toLowerCase($value) : $value;
    }

    protected function toLowerCase(string $value): string
    {
        return strtolower($value);
    }

    public function toStringVector(): StringVector
    {
        return new StringVector(...$this->values);
    }

    public function toStringArray(): array
    {
        return $this->getArray();
    }
}