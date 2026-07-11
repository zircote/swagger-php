<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

use OpenApi\Spec as OA;
use OpenApi\Utils\SpecificationWalker;

/**
 * Flat container for all collected spec attributes.
 */
class Specification
{
    public OA\OpenApi $openapi;

    public ?OA\Info $info = null;

    /** @var list<OA\Server> */
    public array $servers = [];

    /** @var list<OA\Tag> */
    public array $tags = [];

    /** @var list<OA\ExternalDocumentation> */
    public array $externalDocs = [];

    /** @var list<OA\Operation> */
    public array $operations = [];

    /** @var list<OA\Schema> */
    public array $schemas = [];

    /** @var list<OA\Response> */
    public array $responses = [];

    /** @var list<OA\Parameter> */
    public array $parameters = [];

    /** @var list<OA\RequestBody> */
    public array $requestBodies = [];

    /** @var list<OA\Header> */
    public array $headers = [];

    /** @var list<OA\Security\Scheme> */
    public array $securitySchemes = [];

    /** @var list<OA\Link> */
    public array $links = [];

    /** @var list<OA\Example> */
    public array $examples = [];

    public function __construct()
    {
        $this->openapi = new OA\OpenApi();
    }

    public function add(AttributeInterface ...$attributes): static
    {
        foreach ($attributes as $attribute) {
            match (true) {
                $attribute instanceof OA\OpenApi => $this->openapi = $attribute,
                $attribute instanceof OA\Info => $this->info = $attribute,
                $attribute instanceof OA\Server => $this->servers[] = $attribute,
                $attribute instanceof OA\Tag => $this->tags[] = $attribute,
                $attribute instanceof OA\ExternalDocumentation => $this->externalDocs[] = $attribute,
                $attribute instanceof OA\Operation => $this->operations[] = $attribute,
                $attribute instanceof OA\Schema => $this->schemas[] = $attribute,
                $attribute instanceof OA\Response => $this->responses[] = $attribute,
                $attribute instanceof OA\Parameter => $this->parameters[] = $attribute,
                $attribute instanceof OA\RequestBody => $this->requestBodies[] = $attribute,
                $attribute instanceof OA\Header => $this->headers[] = $attribute,
                $attribute instanceof OA\Security\Scheme => $this->securitySchemes[] = $attribute,
                $attribute instanceof OA\Link => $this->links[] = $attribute,
                $attribute instanceof OA\Example => $this->examples[] = $attribute,
                default => throw OpenApiException::fromSource(
                    'Unsupported root-level attribute: ' . $attribute::class,
                    $attribute->getSourceLocation(),
                ),
            };
        }

        return $this;
    }

    public function getWalker(): SpecificationWalker
    {
        return new SpecificationWalker($this);
    }
}
