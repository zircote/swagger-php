<?php declare(strict_types=1);

namespace OpenApi\Tests\Fixtures\PHP;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Label
{
    protected $name;

    public function __construct(string $name, array $numbers)
    {
        $this->name = $name;
    }
}