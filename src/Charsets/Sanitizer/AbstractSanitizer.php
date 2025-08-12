<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Charsets\Sanitizer;

use Charcoal\Base\Charsets\Sanitizer\Exception\CharsetSanitizerError;
use Charcoal\Base\Charsets\Sanitizer\Exception\CharsetSanitizerException;
use Charcoal\Base\Contracts\Charsets\SanitizerModifierInterface;
use Charcoal\Base\Enums\Charset;
use Charcoal\Base\Support\EnumHelper;

/**
 * Abstract class for sanitization, providing methods to apply and validate a set of rules
 * on input data. This class is designed to be extended and customized for specific use cases.
 */
abstract class AbstractSanitizer
{
    protected ?int $minLength = null;
    protected ?int $maxLength = null;
    protected ?int $exactLength = null;

    /** @var array<string> */
    protected array $matchTokens = [];
    /** @var array<string> */
    protected array $matchRegExp = [];
    /** @var array<SanitizerModifierInterface> */
    protected array $modifiers = [];
    /** @var array<\Closure> */
    protected array $modifierCallbacks = [];
    /** @var array<\Closure> */
    protected array $validationCallbacks = [];

    /**
     * @param Charset $charset
     * @param bool $onErrorCaptureSubject
     */
    public function __construct(
        public readonly Charset $charset,
        public bool             $onErrorCaptureSubject = false
    )
    {
    }

    /**
     * @param string $result
     * @return string
     * @throws CharsetSanitizerException
     */
    abstract protected function validateCharset(string $result): string;

    /**
     * @param int $exact
     * @return $this
     */
    public function lengthExact(int $exact): static
    {
        if ($exact < 0) {
            throw new \InvalidArgumentException("Length must be greater than 0");
        }

        $this->exactLength = $exact;
        $this->minLength = null;
        $this->maxLength = null;
        return $this;
    }

    /**
     * @param int $min
     * @param int $max
     * @return $this
     */
    public function lengthRange(int $min = 0, int $max = 0): static
    {
        if ($min < 0 || $max < 0 || $min > $max) {
            throw new \InvalidArgumentException("Invalid minimum and maximum length");
        }

        $this->exactLength = null;
        $this->minLength = $min;
        $this->maxLength = $max;
        return $this;
    }

    /**
     * @param SanitizerModifierInterface ...$modifiers
     * @return $this
     */
    public function modifiers(SanitizerModifierInterface ...$modifiers): static
    {
        $this->modifiers = [...$this->modifiers, ...$modifiers];
        return $this;
    }

    /**
     * @param string ...$regExps
     * @return $this
     */
    public function matchRegEx(string ...$regExps): static
    {
        if (!$regExps) {
            return $this;
        }

        foreach ($regExps as $regExp) {
            $this->matchRegExp[] = $regExp;
        }

        return $this;
    }

    /**
     * @param string ...$opts
     * @return $this
     */
    public function existsInSet(string ...$opts): static
    {
        $this->matchTokens = $opts;
        return $this;
    }

    /**
     * @param \Closure $callback
     * @return $this
     */
    public function callbackValidation(\Closure $callback): static
    {
        $this->validationCallbacks[] = $callback;
        return $this;
    }

    /**
     * @param \Closure $callback
     * @return $this
     */
    public function callbackModifier(\Closure $callback): static
    {
        $this->modifierCallbacks[] = $callback;
        return $this;
    }

    /**
     * @param mixed $value
     * @param bool $emptyStringNull
     * @return string|null
     * @throws CharsetSanitizerException
     */
    public function getNullable(mixed $value, bool $emptyStringNull = true): ?string
    {
        if (is_null($value) || ($emptyStringNull && $value === "")) {
            return null;
        }

        return $this->getProcessed($value);
    }

    /**
     * @param mixed $value
     * @return string
     * @throws CharsetSanitizerException
     */
    public function getProcessed(mixed $value): string
    {
        // Type
        if (!is_string($value)) {
            throw new CharsetSanitizerException(CharsetSanitizerError::TYPE_ERROR);
        }

        // Modifiers
        $this->modifiers = EnumHelper::filterUniqueFromSet(...$this->modifiers);
        if ($this->modifiers) {
            foreach ($this->modifiers as $modifier) {
                $value = $modifier->apply($value, $this->charset);
            }
        }

        // Modifier Closures
        if ($this->modifierCallbacks) {
            $capture = $value;
            $index = -1;
            foreach ($this->modifierCallbacks as $callback) {
                $index++;
                $value = call_user_func($callback, $value);
                if (!is_string($value)) {
                    throw new CharsetSanitizerException(CharsetSanitizerError::MODIFIER_CALLBACK_TYPE_ERROR,
                        subject: $capture, index: $index);
                }

                $capture = $value;
            }
        }

        unset($capture, $index);

        // Charset Check
        $value = $this->validateCharset($value);

        // Check length
        $length = match ($this->charset) {
            Charset::ASCII => strlen($value),
            Charset::UTF8 => mb_strlen($value, "UTF-8"),
        };

        if (is_int($this->exactLength)) {
            if ($length !== $this->exactLength) {
                throw new CharsetSanitizerException(CharsetSanitizerError::LENGTH_ERROR,
                    subject: $value
                );
            }
        } elseif ($this->minLength || $this->maxLength) {
            if ($this->minLength && $length < $this->minLength) {
                throw new CharsetSanitizerException(CharsetSanitizerError::LENGTH_UNDERFLOW_ERROR,
                    subject: $value
                );
            }

            if ($this->maxLength && $length > $this->maxLength) {
                throw new CharsetSanitizerException(CharsetSanitizerError::LENGTH_OVERFLOW_ERROR,
                    subject: $value
                );
            }
        }

        unset($length);

        // RegExp Validations
        if ($this->matchRegExp) {
            $index = -1;
            foreach ($this->matchRegExp as $regExp) {
                $index++;
                if (!preg_match($regExp, $value)) {
                    throw new CharsetSanitizerException(CharsetSanitizerError::REGEXP_MATCH_ERROR,
                        subject: $value, index: $index
                    );
                }
            }

            unset($index);
        }

        // Check if is in defined Array
        if ($this->matchTokens) {
            if (!in_array($value, $this->matchTokens)) {
                throw new CharsetSanitizerException(CharsetSanitizerError::ENUM_ERROR, subject: $value);
            }
        }

        // Modifier Closures
        if ($this->validationCallbacks) {
            $index = -1;
            foreach ($this->validationCallbacks as $callback) {
                $index++;
                $result = call_user_func($callback, $value);
                if (!is_bool($result)) {
                    throw new CharsetSanitizerException(CharsetSanitizerError::VALIDATOR_CALLBACK_TYPE_ERROR,
                        subject: $value, index: $index);
                }

                if (!$result) {
                    throw new CharsetSanitizerException(CharsetSanitizerError::VALIDATOR_CALLBACK_FAILED,
                        subject: $value, index: $index);
                }
            }

            unset($index, $result);
        }

        return $value;
    }
}