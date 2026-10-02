<?php

namespace OpenApi\Rector\Tests\Fixture\TypeClassStringToRef;

use OpenApi\Attributes as OA;

class PetList
{
    #[OA\Property(items: new OA\Items(type: Pet::class))]
    public array $pets;

    #[OA\Property(type: Pet::class)]
    public Pet $pet;
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\TypeClassStringToRef;

use OpenApi\Attributes as OA;

class PetList
{
    #[OA\Property(items: new OA\Items(ref: Pet::class))]
    public array $pets;

    #[OA\Property(ref: Pet::class)]
    public Pet $pet;
}

?>
