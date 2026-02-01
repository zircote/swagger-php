<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Petstore\Attributes\Controllers;

use OpenApi\Attributes as OAT;
use OpenApi\Examples\Specs\Petstore\Attributes\Models\ApiResponse;
use OpenApi\Examples\Specs\Petstore\Attributes\Models\Pet;
use OpenApi\Examples\Specs\Petstore\Attributes\Models\PetRequestBody;

/**
 * Class Pet.
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class PetController
{
    /**
     * Add a new pet to the store.
     */
    #[OAT\Post(path: '/pet', operationId: 'addPet', security: [
        [
            'petstore_auth' => [
                'write:pets',
                'read:pets',
            ],
        ],
    ], requestBody: new OAT\RequestBody(
        ref: PetRequestBody::class
    ), tags: ['pet'], responses: [
        new OAT\Response(
            response: 405,
            description: 'Invalid input'
        ),
    ])]
    public function addPet()
    {
    }

    /**
     * Update an existing pet.
     */
    #[OAT\Put(path: '/pet', operationId: 'updatePet', security: [
        [
            'petstore_auth' => [
                'write:pets',
                'read:pets',
            ],
        ],
    ], requestBody: new OAT\RequestBody(
        ref: PetRequestBody::class
    ), tags: ['pet'], responses: [
        new OAT\Response(
            response: 400,
            description: 'Invalid ID supplied'
        ),
        new OAT\Response(
            response: 404,
            description: 'Pet not found'
        ),
        new OAT\Response(
            response: 405,
            description: 'Validation exception'
        ),
    ])]
    public function updatePet()
    {
    }

    #[OAT\Get(path: '/pet/findByStatus', operationId: 'findPetsByStatus', description: 'Multiple status values can be provided with comma separated string', summary: 'Finds Pets by status', security: [
        [
            'petstore_auth' => [
                'write:pets',
                'read:pets',
            ],
        ],
    ], tags: ['pet'], parameters: [
        new OAT\Parameter(
            name: 'status',
            description: 'Status values that needed to be considered for filter',
            in: 'query',
            required: true,
            schema: new OAT\Schema(
                type: 'string',
                default: 'available',
                enum: ['available', 'pending', 'sold']
            ),
            explode: true
        ),
    ], responses: [
        new OAT\Response(
            response: 200,
            description: 'successful operation',
            content: [
                new OAT\JsonContent(
                    type: 'array',
                    items: new OAT\Items(
                        ref: Pet::class
                    )
                ),
                new OAT\XmlContent(
                    type: 'array',
                    items: new OAT\Items(
                        ref: Pet::class
                    )
                ),
            ]
        ),
        new OAT\Response(
            response: 400,
            description: 'Invalid status value'
        ),
    ], deprecated: true)]
    public function findPetsByStatus()
    {
    }

    #[OAT\Get(path: '/pet/findByTags', operationId: 'findByTags', description: 'Muliple tags can be provided with comma separated strings. Use tag1, tag2, tag3 for testing.', summary: 'Finds Pets by tags', security: [
        [
            'petstore_auth' => [
                'write:pets',
                'read:pets',
            ],
        ],
    ], tags: ['pet'], parameters: [
        new OAT\Parameter(
            name: 'tags',
            description: 'Tags to filter by',
            in: 'query',
            required: true,
            schema: new OAT\Schema(
                type: 'array',
                items: new OAT\Items(
                    type: 'string'
                )
            ),
            explode: true
        ),
    ], responses: [
        new OAT\Response(
            response: 200,
            description: 'successful operation',
            content: [
                new OAT\JsonContent(
                    type: 'array',
                    items: new OAT\Items(
                        ref: Pet::class
                    )
                ),
                new OAT\XmlContent(
                    type: 'array',
                    items: new OAT\Items(
                        ref: Pet::class
                    )
                ),
            ]
        ),
        new OAT\Response(
            response: 400,
            description: 'Invalid tag value'
        ),
    ])]
    public function findByTags()
    {
    }

    #[OAT\Get(path: '/pet/{petId}', operationId: 'getPetById', description: 'Returns a single pet', summary: 'Find pet by ID', security: [
        [
            'api_key' => [
            ],
        ],
    ], tags: ['pet'], parameters: [
        new OAT\Parameter(
            name: 'petId',
            description: 'ID of pet to return',
            in: 'path',
            required: true,
            schema: new OAT\Schema(
                type: 'integer',
                format: 'int64'
            )
        ),
    ], responses: [
        new OAT\Response(
            response: 200,
            description: 'successful operation',
            content: [
                new OAT\JsonContent(
                    ref: Pet::class
                ),
                new OAT\XmlContent(
                    ref: Pet::class
                ),
            ]
        ),
        new OAT\Response(
            response: 400,
            description: 'Invalid ID supplier'
        ),
        new OAT\Response(
            response: 404,
            description: 'Pet not found'
        ),
    ])]
    public function getPetById(int $id)
    {
    }

    #[OAT\Post(path: '/pet/{petId}', operationId: 'updatePetWithForm', summary: 'Updates a pet in the store with form data', security: [
        [
            'petstore_auth' => [
                'write:pets',
                'read:pets',
            ],
        ],
    ], requestBody: new OAT\RequestBody(
        description: 'Input data format',
        content: new OAT\MediaType(
            mediaType: 'application/x-www-form-urlencoded',
            schema: new OAT\Schema(
                properties: [
                    new OAT\Property(
                        property: 'name',
                        description: 'Updated name of the pet',
                        type: 'string'
                    ),
                    new OAT\Property(
                        property: 'status',
                        description: 'Updated status of the pet',
                        type: 'string'
                    ),
                ],
                type: 'object'
            )
        )
    ), tags: ['pet'], parameters: [
        new OAT\Parameter(
            name: 'petId',
            description: 'ID of pet that needs to be updated',
            in: 'path',
            required: true,
            schema: new OAT\Schema(
                type: 'integer',
                format: 'int64'
            )
        ),
    ], responses: [
        new OAT\Response(
            response: 405,
            description: 'Invalid input'
        ),
    ])]
    public function updatePetWithForm()
    {
    }

    #[OAT\Delete(path: '/pet/{petId}', operationId: 'deletePet', summary: 'Deletes a pet', security: [
        [
            'petstore_auth' => [
                'write:pets',
                'read:pets',
            ],
        ],
    ], tags: ['pet'], parameters: [
        new OAT\Parameter(
            name: 'api_key',
            in: 'header',
            required: false,
            schema: new OAT\Schema(
                type: 'string'
            )
        ),
        new OAT\Parameter(
            name: 'petId',
            description: 'Pet id to delete',
            in: 'path',
            required: true,
            schema: new OAT\Schema(
                type: 'integer',
                format: 'int64'
            )
        ),
    ], responses: [
        new OAT\Response(
            response: 400,
            description: 'Invalid ID supplied'
        ),
        new OAT\Response(
            response: 404,
            description: 'Pet not found'
        ),
    ])]
    public function deletePet()
    {
    }

    #[OAT\Post(path: '/pet/{petId}/uploadImage', operationId: 'uploadFile', summary: 'uploads an image', security: [
        [
            'petstore_auth' => [
                'write:pets',
                'read:pets',
            ],
        ],
    ], requestBody: new OAT\RequestBody(
        description: 'Upload images request body',
        content: new OAT\MediaType(
            mediaType: 'application/octet-stream',
            schema: new OAT\Schema(
                type: 'string',
                format: 'binary'
            )
        )
    ), tags: ['pet'], parameters: [
        new OAT\Parameter(
            name: 'petId',
            description: 'ID of pet to update',
            in: 'path',
            required: true,
            schema: new OAT\Schema(
                type: 'integer',
                format: 'int64',
                example: 1
            )
        ),
    ], responses: [
        new OAT\Response(
            response: 200,
            description: 'successful operation',
            content: new OAT\JsonContent(
                ref: ApiResponse::class
            )
        ),
    ])]
    public function uploadFile()
    {
    }
}
