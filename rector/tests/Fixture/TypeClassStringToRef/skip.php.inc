<?php

namespace OpenApi\Rector\Tests\Fixture\TypeClassStringToRef;

use OpenApi\Attributes as OA;

class Untouched
{
    #[OA\Property(type: 'string')]
    public string $name;

    #[OA\Property(type: Pet::class, ref: '#/components/schemas/Other')]
    public Pet $both;

    #[\Other\Property(type: Pet::class)]
    public Pet $foreign;
}
