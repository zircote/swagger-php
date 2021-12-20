<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS)]
class SecurityScheme extends \OpenApi\Annotations\SecurityScheme
{
    public function __construct(
        string $ref = Generator::UNDEFINED,
        string $securityScheme = Generator::UNDEFINED,
        string $type = Generator::UNDEFINED,
        string $description = Generator::UNDEFINED,
        string $name = Generator::UNDEFINED,
        string $in = Generator::UNDEFINED,
        string $bearerFormat = Generator::UNDEFINED,
        string $scheme = Generator::UNDEFINED,
        string $openIdConnectUrl = Generator::UNDEFINED,
        ?array $flows = null,
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'ref' => $ref,
                'securityScheme' => $securityScheme,
                'type' => $type,
                'description' => $description,
                'name' => $name,
                'in' => $in,
                'bearerFormat' => $bearerFormat,
                'scheme' => $scheme,
                'openIdConnectUrl' => $openIdConnectUrl,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($flows, $attachables),
            ]);
    }
}
