<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Petstore\Spec\Controllers;

use OpenApi\Examples\Specs\Petstore\Spec\Models\ApiResponse;
use OpenApi\Examples\Specs\Petstore\Spec\Models\Pet;
use OpenApi\Examples\Specs\Petstore\Spec\Models\PetRequestBody;
use OpenApi\Spec as OA;

/**
 * Class Pet.
 */
class PetController
{
    /**
     * Add a new pet to the store.
     */
    #[OA\Operation\Post(path: '/pet', operationId: 'addPet', tags: ['pet'], requestBody: new OA\RequestBody(ref: PetRequestBody::class), security: [
        new OA\Security\Requirement(scheme: 'petstore_auth', scopes: ['write:pets', 'read:pets']),
    ])]
    #[OA\Response(response: 405, description: 'Invalid input')]
    public function addPet()
    {
    }

    /**
     * Update an existing pet.
     */
    #[OA\Operation\Put(path: '/pet', operationId: 'updatePet', tags: ['pet'], requestBody: new OA\RequestBody(ref: PetRequestBody::class), security: [
        new OA\Security\Requirement(scheme: 'petstore_auth', scopes: ['write:pets', 'read:pets']),
    ])]
    #[OA\Response(response: 400, description: 'Invalid ID supplied')]
    #[OA\Response(response: 404, description: 'Pet not found')]
    #[OA\Response(response: 405, description: 'Validation exception')]
    public function updatePet()
    {
    }

    #[OA\Operation\Get(path: '/pet/findByStatus', operationId: 'findPetsByStatus', summary: 'Finds Pets by status', description: 'Multiple status values can be provided with comma separated string', tags: ['pet'], parameters: [
        new OA\Parameter(
            name: 'status',
            in: OA\ParameterIn::Query,
            description: 'Status values that needed to be considered for filter',
            required: true,
            explode: true,
            schema: new OA\Schema(type: 'string', enum: ['available', 'pending', 'sold'], default: 'available'),
        ),
    ], deprecated: true, security: [
        new OA\Security\Requirement(scheme: 'petstore_auth', scopes: ['write:pets', 'read:pets']),
    ])]
    #[OA\Response(
        response: 200,
        description: 'successful operation',
        content: [
            new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema\Items(ref: Pet::class)),
            new OA\MediaType(mediaType: 'application/xml', schema: new OA\Schema(type: 'array', items: new OA\Schema(ref: Pet::class))),
        ],
    )]
    #[OA\Response(response: 400, description: 'Invalid status value')]
    public function findPetsByStatus()
    {
    }

    #[OA\Operation\Get(path: '/pet/findByTags', operationId: 'findByTags', summary: 'Finds Pets by tags', description: 'Muliple tags can be provided with comma separated strings. Use tag1, tag2, tag3 for testing.', tags: ['pet'], parameters: [
        new OA\Parameter(
            name: 'tags',
            in: 'query',
            description: 'Tags to filter by',
            required: true,
            explode: true,
            schema: new OA\Schema(type: 'array', items: new OA\Schema(type: 'string')),
        ),
    ], security: [
        new OA\Security\Requirement(scheme: 'petstore_auth', scopes: ['write:pets', 'read:pets']),
    ])]
    #[OA\Response(
        response: 200,
        description: 'successful operation',
        content: [
            new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(type: 'array', items: new OA\Schema(ref: Pet::class))),
            new OA\MediaType(mediaType: 'application/xml', schema: new OA\Schema(type: 'array', items: new OA\Schema(ref: Pet::class))),
        ],
    )]
    #[OA\Response(response: 400, description: 'Invalid tag value')]
    public function findByTags()
    {
    }

    #[OA\Operation\Get(path: '/pet/{petId}', operationId: 'getPetById', summary: 'Find pet by ID', description: 'Returns a single pet', tags: ['pet'], parameters: [
        new OA\Parameter(
            name: 'petId',
            in: OA\ParameterIn::Path,
            description: 'ID of pet to return',
            required: true,
            schema: new OA\Schema(type: 'integer', format: 'int64'),
        ),
    ], security: [
        new OA\Security\Requirement(scheme: 'api_key', scopes: []),
    ])]
    #[OA\Response(
        response: 200,
        description: 'successful operation',
        content: [
            new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(ref: Pet::class)),
            new OA\MediaType(mediaType: 'application/xml', schema: new OA\Schema(ref: Pet::class)),
        ],
    )]
    #[OA\Response(response: 400, description: 'Invalid ID supplier')]
    #[OA\Response(response: 404, description: 'Pet not found')]
    public function getPetById(int $id)
    {
    }

    #[OA\Operation\Post(path: '/pet/{petId}', operationId: 'updatePetWithForm', summary: 'Updates a pet in the store with form data', tags: ['pet'], parameters: [
        new OA\Parameter(
            name: 'petId',
            in: 'path',
            description: 'ID of pet that needs to be updated',
            required: true,
            schema: new OA\Schema(type: 'integer', format: 'int64'),
        ),
    ], requestBody: new OA\RequestBody(
        description: 'Input data format',
        content: [
            new OA\MediaType(
                mediaType: 'application/x-www-form-urlencoded',
                schema: new OA\Schema(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'name', schema: new OA\Schema(description: 'Updated name of the pet', type: 'string')),
                        new OA\Property(property: 'status', schema: new OA\Schema(description: 'Updated status of the pet', type: 'string')),
                    ],
                ),
            ),
        ],
    ), security: [
        new OA\Security\Requirement(scheme: 'petstore_auth', scopes: ['write:pets', 'read:pets']),
    ])]
    #[OA\Response(response: 405, description: 'Invalid input')]
    public function updatePetWithForm()
    {
    }

    #[OA\Operation\Delete(path: '/pet/{petId}', operationId: 'deletePet', summary: 'Deletes a pet', tags: ['pet'], parameters: [
        new OA\Parameter(name: 'api_key', in: 'header', required: false, schema: new OA\Schema(type: 'string')),
        new OA\Parameter(name: 'petId', in: 'path', description: 'Pet id to delete', required: true, schema: new OA\Schema(type: 'integer', format: 'int64')),
    ], security: [
        new OA\Security\Requirement(scheme: 'petstore_auth', scopes: ['write:pets', 'read:pets']),
    ])]
    #[OA\Response(response: 400, description: 'Invalid ID supplied')]
    #[OA\Response(response: 404, description: 'Pet not found')]
    public function deletePet()
    {
    }

    #[OA\Operation\Post(path: '/pet/{petId}/uploadImage', operationId: 'uploadFile', summary: 'uploads an image', tags: ['pet'], parameters: [
        new OA\Parameter(
            name: 'petId',
            in: 'path',
            description: 'ID of pet to update',
            required: true,
            schema: new OA\Schema(type: 'integer', format: 'int64', example: 1),
        ),
    ], requestBody: new OA\RequestBody(
        description: 'Upload images request body',
        content: [
            new OA\MediaType(
                mediaType: 'application/octet-stream',
                schema: new OA\Schema(type: 'string', format: 'binary'),
            ),
        ],
    ), security: [
        new OA\Security\Requirement(scheme: 'petstore_auth', scopes: ['write:pets', 'read:pets']),
    ])]
    #[OA\Response(
        response: 200,
        description: 'successful operation',
        content: [new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(ref: ApiResponse::class))],
    )]
    public function uploadFile()
    {
    }
}
