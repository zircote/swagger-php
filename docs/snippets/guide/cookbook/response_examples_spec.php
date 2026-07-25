<?php

namespace Openapi\Snippets\Cookbook\ResponseExamples;

use OpenApi\Spec as OA;

#[OA\Schema(
    schema: 'Result',
    type: 'object',
    properties: [
        new OA\Property(property: 'success', schema: new OA\Schema(type: 'boolean')),
    ],
)]
class ResultModel
{
}

class Controller
{
    #[OA\Response(
        response: 200,
        description: 'OK',
        content: [
            new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    oneOf: [
                        new OA\Schema(ref: '#/components/schemas/Result'),
                        new OA\Schema(type: 'boolean'),
                    ]
                ),
                examples: [
                    new OA\Example(example: 'result', value: ['success' => true], summary: 'An result object.'),
                    new OA\Example(example: 'bool', value: false, summary: 'A boolean value.'),
                ],
            )],
    )]
    public function operation()
    {
    }
}
