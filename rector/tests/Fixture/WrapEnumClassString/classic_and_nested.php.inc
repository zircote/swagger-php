<?php

namespace OpenApi\Rector\Tests\Fixture\WrapEnumClassString;

use OpenApi\Attributes as OA;

enum Status: string
{
    case Available = 'available';
}

#[OA\Schema(enum: Status::class)]
class Entry
{
    #[OA\Property(items: new OA\Items(enum: Status::class))]
    public array $statuses;
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\WrapEnumClassString;

use OpenApi\Attributes as OA;

enum Status: string
{
    case Available = 'available';
}

#[OA\Schema(enum: [Status::class])]
class Entry
{
    #[OA\Property(items: new OA\Items(enum: [Status::class]))]
    public array $statuses;
}

?>
