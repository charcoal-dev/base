<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Vectors;

use Charcoal\Base\Contracts\Vectors\StringVectorProviderInterface;
use Charcoal\Base\Support\EnumHelper;

/**
 * Provides functionality for managing a collection of enumeration values,
 * including optional enforcement of unique tokens only.
 * @see StringVectorProviderInterface
 */
abstract class AbstractEnumVector extends AbstractVector
{
    protected bool $sorting = true;

    /**
     * @param \UnitEnum ...$values
     */
    protected function __construct(\UnitEnum ...$values)
    {
        parent::__construct($values);
    }

    /**
     * @return $this
     */
    public function filterUnique(): static
    {
        /** @var \UnitEnum[] $values */
        $values = $this->values;
        $this->values = EnumHelper::filterUniqueFromSet(...$values);
        return $this;
    }

    /**
     * @param int|class-string $index
     * @param null|array<string, array<string, int|string|null>> $classmap
     * @return null|array<int|string|null>
     * @api
     */
    protected function getCaseValues(int|string $index, ?array $classmap = null): ?array
    {
        $cases = $this->getCaseMap($index, $classmap);
        if (!is_array($cases)) {
            return null;
        }

        if ($this->sorting && $cases) {
            sort($cases, is_int($cases[0]) ? SORT_NUMERIC : SORT_STRING);
        }

        return $cases;
    }

    /**
     * @param int|class-string $index
     * @param null|array<string, array<string, int|string|null>> $classmap
     * @return null|string[]
     * @api
     */
    protected function getCaseNames(int|string $index, ?array $classmap = null): ?array
    {
        $cases = $this->getCaseMap($index, $classmap);
        return is_array($cases) ? array_keys($cases) : null;
    }

    /**
     * @param int|class-string $index
     * @param null|array<string, array<string, int|string|null>> $classmap
     * @return null|array<string, int|string|null>
     */
    protected function getCaseMap(int|string $index, ?array $classmap = null): ?array
    {
        if ((is_int($index) && $index < 0) || $index === "") {
            return null;
        }

        $classmap ??= $this->createEnumsClassmap();
        return is_string($index) ? $classmap[$index] ?? null :
            array_values($classmap)[$index] ?? null;
    }

    /**
     * @return array<string, array<string, int|string|null>>
     */
    protected function createEnumsClassmap(): array
    {
        $classes = [];
        foreach ($this->values as $value) {
            $classes[$value::class] ??= [];
            $classes[$value::class][$value->name] = $value instanceof \BackedEnum ? $value->value : null;
        }

        if ($this->sorting) {
            ksort($classes);
            array_walk($classes, function (&$value) {
                ksort($value);
            });
        }

        return $classes;
    }
}