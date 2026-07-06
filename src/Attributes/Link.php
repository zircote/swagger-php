<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Annotations as OA;
use OpenApi\Undefined;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Link extends OA\Link
{
    /**
     * @param string|class-string|object|null $ref
     * @param array<string,mixed>             $parameters
     * @param array<string,mixed>|null        $x
     * @param list<Attachable>|null           $attachables
     */
    public function __construct(
        ?string $link = null,
        ?string $operationRef = null,
        string|object|null $ref = null,
        ?string $operationId = null,
        ?array $parameters = null,
        mixed $requestBody = null,
        ?string $description = Undefined::UNDEFINED,
        ?Server $server = null,

        // abstract annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'link' => $link ?? Undefined::UNDEFINED,
                'operationRef' => $operationRef ?? Undefined::UNDEFINED,
                'ref' => $ref ?? Undefined::UNDEFINED,
                'operationId' => $operationId ?? Undefined::UNDEFINED,
                'parameters' => $parameters ?? Undefined::UNDEFINED,
                'requestBody' => $requestBody ?? Undefined::UNDEFINED,
                'description' => $description,
                'x' => $x ?? Undefined::UNDEFINED,
                'attachables' => $attachables ?? Undefined::UNDEFINED,
                'value' => $this->combine($server),
            ]);
    }
}
