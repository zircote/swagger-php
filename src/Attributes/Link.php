<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Link extends \OpenApi\Annotations\Link
{
    /**
     * @param array<string,mixed>       $parameters
     * @param array<string,string>|null $x
     * @param Attachable[]|null         $attachables
     */
    public function __construct(
        ?string $link = null,
        string|object|null $ref = null,
        ?string $operationId = null,
        ?array $parameters = null,
        // annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'link' => $link ?? Generator::UNDEFINED,
                'ref' => $ref ?? Generator::UNDEFINED,
                'operationId' => $operationId ?? Generator::UNDEFINED,
                'parameters' => $parameters ?? Generator::UNDEFINED,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($attachables),
            ]);
    }
}
