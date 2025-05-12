<?php

use OpenApi\Attributes as OA;

class Controller
{
    #[OA\Schema(
        schema: 'Result',
        type: 'object',
        properties: [
            new OA\Property(property: 'success', type: 'boolean'),
        ],
    )]
    #[OA\Response(
        response: 200,
        description: 'OK',
        content: new OA\JsonContent(
            oneOf: [
                new OA\Schema(ref: "#/components/schemas/Result"),
                new OA\Schema(type: "boolean"),
            ],
            examples: [
                new OA\Examples(example: "result", value: ["success" => true], summary: "An result object."),
                new OA\Examples(example: "bool", value: false, summary: "A boolean value."),
            ],
        ),
    )]
    public function operation() {}
}
