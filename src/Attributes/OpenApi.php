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
     * @param Server[]|null            $servers
     * @param Tag[]|null               $tags
     * @param PathItem[]|null          $paths
     * @param Webhook[]|null           $webhooks
     * @param array<string,mixed>|null $x
     * @param Attachable[]|null        $attachables
     */
    public function __construct(
        string $openapi = self::DEFAULT_VERSION,
        ?Info $info = null,
        ?array $servers = null,
        ?array $security = null,
        ?array $tags = null,
        ?ExternalDocumentation $externalDocs = null,
        ?array $paths = null,
        ?Components $components = null,
        ?array $webhooks = null,
        // annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'openapi' => $openapi,
                'security' => $security ?? Generator::UNDEFINED,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($info, $servers, $tags, $externalDocs, $paths, $components, $webhooks, $attachables),
            ]);
    }
}
