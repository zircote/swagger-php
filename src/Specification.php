<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

use OpenApi\Spec\Security\Scheme;

/**
 * Flat container for all collected spec attributes.
 */
class Specification
{
    public Spec\OpenApi $openapi;

    public ?Spec\Info $info = null;

    /** @var list<Spec\Server> */
    public array $servers = [];

    /** @var list<Spec\Tag> */
    public array $tags = [];

    /** @var list<Spec\ExternalDocumentation> */
    public array $externalDocs = [];

    /** @var list<Spec\Operation> */
    public array $operations = [];

    /** @var list<Spec\Schema> */
    public array $schemas = [];

    /** @var list<Spec\Response> */
    public array $responses = [];

    /** @var list<Spec\Parameter> */
    public array $parameters = [];

    /** @var list<Spec\RequestBody> */
    public array $requestBodies = [];

    /** @var list<Spec\Header> */
    public array $headers = [];

    /** @var list<Scheme> */
    public array $securitySchemes = [];

    /** @var list<Spec\Link> */
    public array $links = [];

    /** @var list<Spec\Example> */
    public array $examples = [];

    public function __construct()
    {
        $this->openapi = new Spec\OpenApi();
    }

    public function add(AttributeInterface ...$attributes): static
    {
        foreach ($attributes as $attribute) {
            match (true) {
                $attribute instanceof Spec\OpenApi => $this->openapi = $attribute,
                $attribute instanceof Spec\Info => $this->info = $attribute,
                $attribute instanceof Spec\Server => $this->servers[] = $attribute,
                $attribute instanceof Spec\Tag => $this->tags[] = $attribute,
                $attribute instanceof Spec\ExternalDocumentation => $this->externalDocs[] = $attribute,
                $attribute instanceof Spec\Operation => $this->operations[] = $attribute,
                $attribute instanceof Spec\Schema => $this->schemas[] = $attribute,
                $attribute instanceof Spec\Response => $this->responses[] = $attribute,
                $attribute instanceof Spec\Parameter => $this->parameters[] = $attribute,
                $attribute instanceof Spec\RequestBody => $this->requestBodies[] = $attribute,
                $attribute instanceof Spec\Header => $this->headers[] = $attribute,
                $attribute instanceof Scheme => $this->securitySchemes[] = $attribute,
                $attribute instanceof Spec\Link => $this->links[] = $attribute,
                $attribute instanceof Spec\Example => $this->examples[] = $attribute,
                default => throw OpenApiException::fromSource(
                    'Unsupported root-level attribute: ' . $attribute::class,
                    $attribute->getSourceLocation(),
                ),
            };
        }

        return $this;
    }
}
