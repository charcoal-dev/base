<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Abstracts\Dataset;

use Charcoal\Base\Enums\ExceptionAction;

/**
 * This class provides a mechanism for processing a batch of data with specified
 * behavior in case of exceptions. It also optionally allows for logging errors
 * through a provided closure.
 *
 * Use ExceptionAction to define how exceptions will be handled during execution.
 * If ExceptionAction::Log is chosen, an error logger closure must be provided.
 * @see ExceptionAction
 */
readonly class BatchEnvelope
{
    /**
     * @param array $items
     * @param ExceptionAction $onError
     * @param (\Closure(string|int, \Throwable): void)|null $errorLogger
     */
    public function __construct(
        public array           $items,
        public ExceptionAction $onError = ExceptionAction::Throw,
        public ?\Closure       $errorLogger = null,
    )
    {
        if ($this->onError === ExceptionAction::Log && $this->errorLogger === null) {
            throw new \InvalidArgumentException("Error logger must be provided when logging errors");
        }
    }
}