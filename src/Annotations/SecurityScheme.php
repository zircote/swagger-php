<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace Swagger\Annotations;

/**
 * @Annotation
 * A "Security Scheme Object": https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#securitySchemeObject
 */
class SecurityScheme extends AbstractAnnotation
{
    /**
     * The key into OpenApi->security array.
     * @var string
     */
    public $securityScheme;

    /**
     * The type of the security scheme.
     * @var string
     */
    public $type;

    /**
     * A short description for security scheme.
     * @var string
     */
    public $description;

    /**
     * The name of the header or query parameter to be used.
     * @var string
     */
    public $name;

    /**
     * Required The location of the API key.
     * @var string
     */
    public $in;

    /**
     * The flow used by the OAuth2 security scheme.
     * @var
     */
    public $flow;

    /**
     * The authorization URL to be used for this flow. This SHOULD be in the form of a URL.
     * @var string
     */
    public $authorizationUrl;

    /**
     * The token URL to be used for this flow. This SHOULD be in the form of a URL.
     * @var string
     */
    public $tokenUrl;

    /**
     * The available scopes for the OAuth2 security scheme.
     * @var Scope[]
     */
    public $scopes;

    /** @inheritdoc */
    public static $_required = ['type'];

    /** @inheritdoc */
    public static $_types = [
        'type' => ['basic', 'apiKey', 'oauth2'],
        'description' => 'string',
        'name' => 'string',
        'in' => ['query', 'header'],
        'flow' => ['implicit', 'password', 'application', 'accessCode'],
        'scopes' => 'object'
    ];

    /** @inheritdoc */
    public static $_parents = [
        'Swagger\Annotations\Components',
    ];
}
