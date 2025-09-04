<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Tests\Fixtures;

use Charcoal\Base\Objects\Traits\NoDumpTrait;
use Charcoal\Base\Objects\Traits\NotCloneableTrait;
use Charcoal\Base\Objects\Traits\NotSerializableTrait;

class CompositeTraitsFixture
{
    use NoDumpTrait;
    use NotCloneableTrait;
    use NotSerializableTrait;

    private readonly \DateTimeImmutable $createdAt;

    public function __construct(
        public readonly string $name,
        public readonly int    $level = 1
    )
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}