<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Components extends \OpenApi\Annotations\Components
{
    /**
     * @param Schema[]|null             $schemas
     * @param Response[]|null           $responses
     * @param Parameter[]|null          $parameters
     * @param RequestBody[]|null        $requestBodies
     * @param Examples[]|null           $examples
     * @param Header[]|null             $headers
     * @param SecurityScheme[]|null     $securitySchemes
     * @param Link[]|null               $links
     * @param callable[]|null           $callbacks
     * @param array<string,string>|null $x
     * @param Attachable[]|null         $attachables
     */
    public function __construct(
        ?array $schemas = null,
        ?array $responses = null,
        ?array $parameters = null,
        ?array $requestBodies = null,
        ?array $examples = null,
        ?array $headers = null,
        ?array $securitySchemes = null,
        ?array $links = null,
        ?array $callbacks = null,
        // annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
            'callbacks' => $callbacks ?? Generator::UNDEFINED,
            'x' => $x ?? Generator::UNDEFINED,
            'attachables' => $attachables ?? Generator::UNDEFINED,
            'value' => $this->combine($schemas, $responses, $parameters, $examples, $requestBodies, $headers, $securitySchemes, $links, $attachables),
        ]);
    }
}
