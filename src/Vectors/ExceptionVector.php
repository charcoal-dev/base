<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Vectors;

/**
 * A vector-like structure that stores exceptions.
 */
class ExceptionVector extends AbstractVector
{
    public function __construct(\Throwable ...$exceptions)
    {
        parent::__construct($exceptions);
    }

    public function append(\Throwable $exception): static
    {
        $this->values[] = $exception;
        return $this;
    }
}