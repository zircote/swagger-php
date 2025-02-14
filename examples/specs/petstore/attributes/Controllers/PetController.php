<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Petstore\Attributes\Controllers;

use OpenApi\Attributes as OAT;
use OpenApi\Examples\Specs\Petstore\Attributes\Models\ApiResponse;
use OpenApi\Examples\Specs\Petstore\Attributes\Models\Pet;

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
    #[OAT\Post(
        path: '/pet',
        tags: ['pet'],
        operationId: 'addPet',
        responses: [
            new OAT\Response(
                response: 405,
                description: 'Invalid input'
            ),
        ],
        security: [
            [
                'petstore_auth' => [
                    'write:pets',
                    'read:pets',
                ],
            ],
        ],
        requestBody: new OAT\RequestBody(
            ref: '#/components/requestBodies/Pet'
        )
    )]
    public function addPet()
    {
    }

    /**
     * Update an existing pet.
     */
    #[OAT\Put(
        path: '/pet',
        tags: ['pet'],
        operationId: 'updatePet',
        responses: [
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
        ],
        security: [
            [
                'petstore_auth' => [
                    'write:pets',
                    'read:pets',
                ],
            ],
        ],
        requestBody: new OAT\RequestBody(
            ref: '#/components/requestBodies/Pet'
        )
    )]
    public function updatePet()
    {
    }

    #[OAT\Get(
        path: '/pet/findByStatus',
        tags: ['pet'],
        summary: 'Finds Pets by status',
        description: 'Multiple status values can be provided with comma separated string',
        operationId: 'findPetsByStatus',
        deprecated: true,
        parameters: [
            new OAT\Parameter(
                name: 'status',
                in: 'query',
                description: 'Status values that needed to be considered for filter',
                required: true,
                explode: true,
                schema: new OAT\Schema(
                    type: 'string',
                    enum: ['available', 'pending', 'sold'],
                    default: 'available'
                )
            ),
        ],
        responses: [
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
        ],
        security: [
            [
                'petstore_auth' => [
                    'write:pets',
                    'read:pets',
                ],
            ],
        ],
    )]
    public function findPetsByStatus()
    {
    }

    #[OAT\Get(
        path: '/pet/findByTags',
        tags: ['pet'],
        summary: 'Finds Pets by tags',
        description: 'Muliple tags can be provided with comma separated strings. Use tag1, tag2, tag3 for testing.',
        operationId: 'findByTags',
        parameters: [
            new OAT\Parameter(
                name: 'tags',
                in: 'query',
                description: 'Tags to filter by',
                required: true,
                explode: true,
                schema: new OAT\Schema(
                    type: 'array',
                    items: new OAT\Items(
                        type: 'string'
                    )
                )
            ),
        ],
        responses: [
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
        ],
        security: [
            [
                'petstore_auth' => [
                    'write:pets',
                    'read:pets',
                ],
            ],
        ],
    )]
    public function findByTags()
    {
    }

    #[OAT\Get(
        path: '/pet/{petId}',
        tags: ['pet'],
        summary: 'Find pet by ID',
        description: 'Returns a single pet',
        operationId: 'getPetById',
        parameters: [
            new OAT\Parameter(
                name: 'petId',
                in: 'path',
                description: 'ID of pet to return',
                required: true,
                schema: new OAT\Schema(
                    type: 'integer',
                    format: 'int64'
                )
            ),
        ],
        responses: [
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
        ],
        security: [
            [
                'api_key' => [
                ],
            ],
        ]
    )]
    public function getPetById(int $id)
    {
    }

    #[OAT\Post(
        path: '/pet/{petId}',
        tags: ['pet'],
        summary: 'Updates a pet in the store with form data',
        operationId: 'updatePetWithForm',
        parameters: [
            new OAT\Parameter(
                name: 'petId',
                in: 'path',
                description: 'ID of pet that needs to be updated',
                required: true,
                schema: new OAT\Schema(
                    type: 'integer',
                    format: 'int64'
                )
            ),
        ],
        responses: [
            new OAT\Response(
                response: 405,
                description: 'Invalid input'
            ),
        ],
        security: [
            [
                'petstore_auth' => [
                    'write:pets',
                    'read:pets',
                ],
            ],
        ],
        requestBody: new OAT\RequestBody(
            description: 'Input data format',
            content: new OAT\MediaType(
                mediaType: 'application/x-www-form-urlencoded',
                schema: new OAT\Schema(
                    type: 'object',
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
                    ]
                )
            )
        )
    )]
    public function updatePetWithForm()
    {
    }

    #[OAT\Delete(
        path: '/pet/{petId}',
        tags: ['pet'],
        summary: 'Deletes a pet',
        operationId: 'deletePet',
        parameters: [
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
                in: 'path',
                description: 'Pet id to delete',
                required: true,
                schema: new OAT\Schema(
                    type: 'integer',
                    format: 'int64'
                )
            ),
        ],
        responses: [
            new OAT\Response(
                response: 400,
                description: 'Invalid ID supplied'
            ),
            new OAT\Response(
                response: 404,
                description: 'Pet not found'
            ),
        ],
        security: [
            [
                'petstore_auth' => [
                    'write:pets',
                    'read:pets',
                ],
            ],
        ],
    )]
    public function deletePet()
    {
    }

    #[OAT\Post(
        path: '/pet/{petId}/uploadImage',
        tags: ['pet'],
        summary: 'uploads an image',
        operationId: 'uploadFile',
        parameters: [
            new OAT\Parameter(
                name: 'petId',
                in: 'path',
                description: 'ID of pet to update',
                required: true,
                schema: new OAT\Schema(
                    type: 'integer',
                    format: 'int64',
                    example: 1
                )
            ),
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'successful operation',
                content: new OAT\JsonContent(
                    ref: ApiResponse::class
                )
            ),
        ],
        security: [
            [
                'petstore_auth' => [
                    'write:pets',
                    'read:pets',
                ],
            ],
        ],
        requestBody: new OAT\RequestBody(
            description: 'Upload images request body',
            content: new OAT\MediaType(
                mediaType: 'application/octet-stream',
                schema: new OAT\Schema(
                    type: 'string',
                    format: 'binary'
                )
            )
        )
    )]
    public function uploadFile()
    {
    }
}
