<?php

namespace OpenApi\Rector\Tests\Fixture\ClassicToSpecSet;

use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Response;

enum Status: string
{
    case Available = 'available';
}

#[OA\OpenApi(
    openapi: '3.0.0',
    info: new OA\Info(title: 'Api', version: '1'),
    tags: [new OA\Tag(name: 'public')],
)]
final class Api
{
}

abstract class AbstractResponse extends Response
{
    public function __construct(
        object|string|null $ref = null,
        int|string|null $response = null,
        ?string $description = null,
        MediaType|JsonContent|array|null $content = null,
    ) {
        parent::__construct($ref, $response ?? 404, $description ?? 'Not found', null, $content);
    }
}

#[OA\Schema(schema: 'PetList', type: 'object')]
final class PetList
{
    #[OA\Property(items: new OA\Items(type: Pet::class))]
    public array $pets;

    #[OA\Property(enum: Status::class, example: 'available')]
    public Status $status;
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\ClassicToSpecSet;

use OpenApi\Spec as OAS;
enum Status: string
{
    case Available = 'available';
}

#[OAS\OpenApi(version: '3.0.0'), OAS\Info(title: 'Api', version: '1'), OAS\Tag(name: 'public')]
final class Api
{
}

abstract class AbstractResponse extends OAS\Response
{
    public function __construct(
        object|string|null $ref = null,
        int|string|null $response = null,
        ?string $description = null,
        OAS\MediaType|OAS\MediaType\Json|array|null $content = null,
    ) {
        parent::__construct(ref: $ref, response: $response ?? 404, description: $description ?? 'Not found', headers: null, content: $content);
    }
}

#[OAS\Schema(schema: 'PetList', type: 'object')]
final class PetList
{
    #[OAS\Property(schema: new OAS\Schema(items: new OAS\Schema\Items(ref: Pet::class)))]
    public array $pets;

    #[OAS\Property(schema: new OAS\Schema(enum: [Status::class], example: 'available'))]
    public Status $status;
}

?>
