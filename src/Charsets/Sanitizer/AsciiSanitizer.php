<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Charsets\Sanitizer;

use Charcoal\Base\Charsets\Ascii;
use Charcoal\Base\Charsets\Sanitizer\Exceptions\CharsetSanitizerError;
use Charcoal\Base\Charsets\Sanitizer\Exceptions\CharsetSanitizerException;
use Charcoal\Base\Enums\Charset;

/**
 * Class AsciiSanitizer
 * Extends AbstractSanitizer to validate and sanitize strings based on the ASCII character set.
 */
class AsciiSanitizer extends AbstractSanitizer
{
    /**
     * @param bool $validatePrintableOnly
     * @param bool $onErrorCaptureSubject
     */
    public function __construct(
        public readonly bool $validatePrintableOnly = true,
        bool                 $onErrorCaptureSubject = false
    )
    {
        parent::__construct(Charset::ASCII, $onErrorCaptureSubject);
    }

    /**
     * @param string $result
     * @return string
     * @throws CharsetSanitizerException
     */
    protected function validateCharset(string $result): string
    {
        $charsetCheck = match ($this->validatePrintableOnly) {
            true => Ascii::isPrintableOnly($result),
            false => Ascii::inCharset($result)
        };

        if (!$charsetCheck) {
            throw new CharsetSanitizerException(CharsetSanitizerError::CHARSET_ERROR);
        }

        return $result;
    }
}