<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Link extends \OpenApi\Annotations\Link
{
    public function __construct(
        string $link = Generator::UNDEFINED,
        string $ref = Generator::UNDEFINED,
        string $operationId = Generator::UNDEFINED,
        ?array $parameters = null,
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'link' => $link,
                'ref' => $ref,
                'operationId' => $operationId,
                'parameters' => $parameters ?? Generator::UNDEFINED,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($attachables),
            ]);
    }
}
