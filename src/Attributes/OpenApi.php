<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS)]
class OpenApi extends \OpenApi\Annotations\OpenApi
{
    /**
     * @param Server[]|null             $servers
     * @param Tag[]|null                $tags
     * @param array<string,string>|null $x
     * @param Attachable[]|null         $attachables
     */
    public function __construct(
        string $openapi = self::DEFAULT_VERSION,
        ?Info $info = null,
        ?array $servers = null,
        ?array $tags = null,
        ?ExternalDocumentation $externalDocs = null,
        // annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'openapi' => $openapi,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($info, $servers, $tags, $externalDocs, $attachables),
            ]);
    }
}
