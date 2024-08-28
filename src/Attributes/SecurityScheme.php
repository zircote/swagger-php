<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;
use OpenApi\Annotations as OA;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class SecurityScheme extends OA\SecurityScheme
{
    /**
     * @param string|non-empty-array<string>|null $type
     * @param string|class-string|object|null     $ref
     * @param Flow[]                              $flows
     * @param array<string,mixed>|null            $x
     * @param Attachable[]|null                   $attachables
     */
    public function __construct(
        string|object|null $ref = null,
        ?string $securityScheme = null,
        string|array|null $type = null,
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
