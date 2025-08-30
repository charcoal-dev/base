<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Support\Helpers;

/**
 * Helper class for handling errors and converting them into exceptions.
 */
abstract readonly class ErrorHelper
{
    /**
     * @return \RuntimeException|null
     */
    public static function lastErrorToRuntimeException(): ?\RuntimeException
    {
        if ($error = error_get_last()) {
            error_clear_last();
            ["type" => $type, "message" => $message, "file" => $file, "line" => $line] = $error;
            return new \RuntimeException(sprintf("[%d] %s in %s@%d", $type, $message, $file, $line), (int)$type);
        }

        return null;
    }

    /**
     * Retrieves the chain of exceptions leading to the provided exception.
     */
    public static function getExceptionChain(\Throwable $exception, bool $reverse = false): array
    {
        $chained = [$exception];
        while ($exception->getPrevious()) {
            $exception = $exception->getPrevious();
            $chained[] = $exception;
        }

        return !$reverse ? $chained : array_reverse($chained);
    }
}