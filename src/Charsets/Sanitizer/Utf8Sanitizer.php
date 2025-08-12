<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Charsets\Sanitizer;

use Charcoal\Base\Charsets\Sanitizer\Exception\CharsetSanitizerError;
use Charcoal\Base\Charsets\Sanitizer\Exception\CharsetSanitizerException;
use Charcoal\Base\Charsets\Utf8;
use Charcoal\Base\Contracts\Charsets\UnicodeLanguageRangeInterface;
use Charcoal\Base\Enums\Charset;

/**
 * Class Utf8Sanitizer
 * Extends the functionality of AbstractSanitizer to provide UTF-8 specific
 * sanitization behavior. This class ensures that input data is properly
 * validated and processed according to UTF-8 standards.
 */
class Utf8Sanitizer extends AbstractSanitizer
{
    private array $unicodeCharsets = [];

    public function __construct(
        public bool $allowAscii = true,
        public bool $allowSpaces = true,
        bool        $onErrorCaptureSubject = false,
    )
    {
        parent::__construct(Charset::UTF8, $onErrorCaptureSubject);
    }

    /**
     * @param UnicodeLanguageRangeInterface ...$ranges
     * @return $this
     */
    public function validateUnicodeRange(UnicodeLanguageRangeInterface ...$ranges): static
    {
        $this->unicodeCharsets = $ranges;
        return $this;
    }

    /**
     * @param string $result
     * @return string
     * @throws CharsetSanitizerException
     */
    protected function validateCharset(string $result): string
    {
        if (!Utf8::validate($result, $this->allowSpaces, $this->allowAscii, ...$this->unicodeCharsets)) {
            throw new CharsetSanitizerException(CharsetSanitizerError::CHARSET_ERROR);
        }

        return $result;
    }
}