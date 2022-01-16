<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS)]
class SecurityScheme extends \OpenApi\Annotations\SecurityScheme
{
    /**
     * @param Flow[]                    $flows
     * @param array<string,string>|null $x
     * @param Attachable[]|null         $attachables
     */
    public function __construct(
        string|object|null $ref = null,
        ?string $securityScheme = null,
        ?string $type = null,
        ?string $description = null,
        ?string $name = null,
        ?string $in = null,
        ?string $bearerFormat = null,
        ?string $scheme = null,
        ?string $openIdConnectUrl = null,
        ?array $flows = null,
        // annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'ref' => $ref ?? Generator::UNDEFINED,
                'securityScheme' => $securityScheme ?? Generator::UNDEFINED,
                'type' => $type ?? Generator::UNDEFINED,
                'description' => $description ?? Generator::UNDEFINED,
                'name' => $name ?? Generator::UNDEFINED,
                'in' => $in ?? Generator::UNDEFINED,
                'bearerFormat' => $bearerFormat ?? Generator::UNDEFINED,
                'scheme' => $scheme ?? Generator::UNDEFINED,
                'openIdConnectUrl' => $openIdConnectUrl ?? Generator::UNDEFINED,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($flows, $attachables),
            ]);
    }
}
