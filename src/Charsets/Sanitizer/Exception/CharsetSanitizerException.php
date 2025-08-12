<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Charsets\Sanitizer\Exception;

/**
 * Represents an exception thrown during charset sanitization operations.
 */
class CharsetSanitizerException extends \Exception
{
    public function __construct(
        public readonly CharsetSanitizerError $errorCode,
        string                                $message = "",
        int                                   $code = 0,
        ?\Throwable                           $previous = null,
        public readonly ?string               $subject = null,
        public readonly ?int                  $index = null,
    )
    {
        parent::__construct($message ?: $this->errorCode->name, $code, $previous);
    }
}