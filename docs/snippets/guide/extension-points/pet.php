<?php declare(strict_types=1);

namespace OpenApi\Snippets\Guide\ExtensionPoints;

use OpenApi\Spec as OA;

#[Dto(of: Pet::class)]
final class Pet
{
    #[OA\Property]
    public string $name = '';

    #[OA\Property]
    public int $age = 0;
}
