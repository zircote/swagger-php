<?php declare(strict_types=1);

namespace OpenApi\Snippets\Guide\WhySpec;

use OpenApi\Compiler\OpenApi31Compiler;
use OpenApi\Spec as OA;
use OpenApi\Specification;

/**
 * A document built from values, with nothing to scan.
 *
 * @param array<string, string> $fields field name => OpenAPI type
 *
 * @return array<string, mixed>
 */
function buildFromFields(array $fields): array
{
    $properties = [];
    foreach ($fields as $name => $type) {
        $properties[] = new OA\Property(
            property: $name,
            schema: new OA\Schema(type: $type),
        );
    }

    $specification = (new Specification())->add(
        new OA\Info(title: 'Tax API', version: '1.0.0'),
        new OA\Schema(schema: 'Tax', properties: $properties),
        new OA\Operation\Get(path: '/taxes', responses: [
            new OA\Response(response: 200, description: 'All taxes', content: [
                new OA\MediaType\Json(schema: new OA\Schema(ref: '#/components/schemas/Tax')),
            ]),
        ]),
    );

    return (new OpenApi31Compiler())->compile($specification);
}
