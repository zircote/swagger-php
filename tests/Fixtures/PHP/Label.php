<?php declare(strict_types=1);

namespace OpenApi\Tests\Fixtures\PHP;

#[\Attribute]
class Label
{
    protected $name;

    public function __construct(string $name, array $numbers)
    {
        $this->name = $name;
    }
}