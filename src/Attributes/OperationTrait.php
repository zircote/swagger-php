<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

trait OperationTrait
{
    /**
     * @param string[]                  $security
     * @param Server[]                  $servers
     * @param Tag[]                     $tags
     * @param Parameter[]               $parameters
     * @param Response[]                $responses
     * @param array<string,string>|null $x
     * @param Attachable[]|null         $attachables
     */
    public function __construct(
        ?string $path = null,
        ?string $operationId = null,
        ?string $description = null,
        ?string $summary = null,
        ?array $security = null,
        ?array $servers = null,
        ?RequestBody $requestBody = null,
        ?array $tags = null,
        ?array $parameters = null,
        ?array $responses = null,
        ?ExternalDocumentation $externalDocs = null,
        // annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'path' => $path ?? Generator::UNDEFINED,
                'operationId' => $operationId ?? Generator::UNDEFINED,
                'description' => $description ?? Generator::UNDEFINED,
                'summary' => $summary ?? Generator::UNDEFINED,
                'security' => $security ?? Generator::UNDEFINED,
                'servers' => $servers ?? Generator::UNDEFINED,
                'tags' => $tags ?? Generator::UNDEFINED,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($requestBody, $responses, $parameters, $externalDocs, $attachables),
            ]);
    }
}
