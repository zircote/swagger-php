<?php

namespace OpenApi\Rector\Tests\Fixture;

use OpenApi\Spec as OAS;

#[OAS\Schema(component: 'Pet')]
class SkipAlreadyAliased
{
}

?>
