<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

trait OperationTrait
{
    public function __construct(
        string $path = Generator::UNDEFINED,
        string $operationId = Generator::UNDEFINED,
        string $description = Generator::UNDEFINED,
        string $summary = Generator::UNDEFINED,
        ?array $security = null,
        ?array $servers = null,
        ?RequestBody $requestBody = null,
        ?array $tags = null,
        ?array $parameters = null,
        ?array $responses = null,
        ?ExternalDocumentation $externalDocs = null,
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'path' => $path,
                'operationId' => $operationId,
                'description' => $description,
                'summary' => $summary,
                'security' => $security ?? Generator::UNDEFINED,
                'servers' => $servers ?? Generator::UNDEFINED,
                'tags' => $tags ?? Generator::UNDEFINED,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($requestBody, $responses, $parameters, $externalDocs, $attachables),
            ]);
    }
}
