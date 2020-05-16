<?php declare(strict_types=1);

namespace OpenApiTests\Fixtures\Context;

use OpenApi\Context;

trait DetectTrait
{
    public function contextFromTrait()
    {
        return Context::detect();
    }

    public function fileFromTrait()
    {
        return __FILE__;
    }
}
