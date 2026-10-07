<?php

namespace OpenApi\Rector\Tests\Fixture\WrapEnumClassString;

use OpenApi\Spec as OAS;

class SpecEntry
{
    #[OAS\Property(schema: new OAS\Schema(enum: Status::class))]
    public Status $status;
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\WrapEnumClassString;

use OpenApi\Spec as OAS;

class SpecEntry
{
    #[OAS\Property(schema: new OAS\Schema(enum: [Status::class]))]
    public Status $status;
}

?>
