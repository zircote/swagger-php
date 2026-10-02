<?php

namespace OpenApi\Rector\Tests\Fixture\WrapEnumClassString;

use OpenApi\Attributes as OA;

#[OA\Schema(enum: [Status::class])]
#[OA\Schema(enum: ['a', 'b'])]
#[\Other\Schema(enum: Status::class)]
class Untouched
{
}
