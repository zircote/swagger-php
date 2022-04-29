<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS)]
class PathItem extends \OpenApi\Annotations\PathItem
{
    /**
     * @param Server[]|null             $servers
     * @param Parameter[]|null          $parameters
     * @param array<string,string>|null $x
     * @param Attachable[]|null         $attachables
     */
    public function __construct(
        ?string $path = null,
        ?string $summary = null,
        ?array $servers = null,
        ?array $parameters = null,
        // annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'path' => $path ?? Generator::UNDEFINED,
                'summary' => $summary ?? Generator::UNDEFINED,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($servers, $parameters, $attachables),
            ]);
    }
}

//Missing parameters: get, put, post, delete, options, head, patch, trace, parameters
