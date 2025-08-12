<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Contracts\Charsets;

use Charcoal\Base\Enums\Charset;

/**
 * SanitizerModifierInterface
 */
interface SanitizerModifierInterface extends \UnitEnum
{
    public function apply(string $value, Charset $charset): string;
}