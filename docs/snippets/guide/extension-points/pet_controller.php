<?php declare(strict_types=1);

namespace OpenApi\Snippets\Guide\ExtensionPoints;

use OpenApi\Spec as OA;

#[OA\Info(title: 'Pets', version: '1.0')]
final class PetController
{
    #[Route(path: '/pets')]
    #[OA\Response(response: 200, description: 'OK')]
    public function list(): void
    {
    }
}
