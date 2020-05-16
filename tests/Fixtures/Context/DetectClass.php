<?php declare(strict_types=1);

namespace OpenApiTests\Fixtures\Context;

use OpenApi\Context;

class DetectClass implements AnInterface
{
    use DetectTrait;

    public function contextFromClass()
    {
        return Context::detect();
    }

    public function fileFromClass()
    {
        return __FILE__;
    }
}
