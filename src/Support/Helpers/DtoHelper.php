<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Support\Helpers;

use Charcoal\Base\Vectors\AbstractTokenVector;

/**
 * A helper class that provides utility methods for working with DTOs.
 */
abstract readonly class DtoHelper
{
    /**
     * @param array|object $object
     * @param int $maxDepth
     * @param bool $normalizeCommonShapes
     * @param bool $checkRecursion
     * @param null|string|callable(object): ?string $onRecursion
     * @return mixed
     */
    public static function createFrom(
        array|object         $object,
        int                  $maxDepth = 10,
        bool                 $normalizeCommonShapes = true,
        bool                 $checkRecursion = true,
        null|string|callable $onRecursion = null
    ): mixed
    {
        if ($maxDepth <= 0) {
            throw new \InvalidArgumentException("Max depth must be greater than 0");
        }

        return self::sanitizeRecursive(
            $checkRecursion ? new \SplObjectStorage() : null,
            $object,
            $maxDepth,
            0,
            $normalizeCommonShapes,
            $onRecursion
        );
    }

    /**
     * @param \SplObjectStorage|null $observer
     * @param array|object $context
     * @param int $maxDepth
     * @param int $depth
     * @param bool $normalizeCommonShapes
     * @param string|callable|null $onRecursion
     * @return mixed
     */
    protected static function sanitizeRecursive(
        ?\SplObjectStorage   $observer,
        array|object         $context,
        int                  $maxDepth,
        int                  $depth,
        bool                 $normalizeCommonShapes,
        null|string|callable $onRecursion,
    ): mixed
    {
        if ($depth >= $maxDepth) {
            return null;
        }

        if (!$context) {
            return [];
        }

        if (is_object($context)) {
            if ($observer?->contains($context)) {
                return is_callable($onRecursion) ? $onRecursion($context) : $onRecursion;
            }

            $observer?->attach($context);

            if ($normalizeCommonShapes) {
                if ($context instanceof \UnitEnum) {
                    return $context instanceof \BackedEnum ?
                        $context->value : $context->name;
                } elseif ($context instanceof \DateTimeInterface) {
                    return $context->format(DATE_ATOM);
                }

                $context = match (true) {
                    $context instanceof \JsonSerializable => $context->jsonSerialize(),
                    $context instanceof \Traversable => iterator_to_array($context, true),
                    $context instanceof AbstractTokenVector => $context->getArray(),
                    default => $context,
                };
            }
        }

        if (is_object($context)) {
            $context = get_object_vars($context);
        }

        if (!is_array($context)) {
            if (is_scalar($context) || is_null($context)) {
                return $context;
            }

            return null;
        }

        foreach ($context as $key => $value) {
            $value = match (true) {
                is_scalar($value), is_null($value) => $value,
                is_array($value), is_object($value) => self::sanitizeRecursive($observer, $value, $maxDepth, $depth + 1,
                    $normalizeCommonShapes, $onRecursion),
                default => null,
            };

            $context[$key] = $value;
        }

        return $context;
    }
}