<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * Configuration details for a supported OAuth Flow
 * [OAuth Flow Object](https://swagger.io/specification/#oauthFlowObject).
 *
 * @Annotation
 */
abstract class AbstractFlow extends AbstractAnnotation
{
    /**
     * The authorization url to be used for this flow.
     * This must be in the form of a url.
     *
     * @var string
     */
    public $authorizationUrl = Generator::UNDEFINED;

    /**
     * The token URL to be used for this flow.
     * This must be in the form of a url.
     *
     * @var string
     */
    public $tokenUrl = Generator::UNDEFINED;

    /**
     * The URL to be used for obtaining refresh tokens.
     * This must be in the form of a url.
     *
     * @var string
     */
    public $refreshUrl = Generator::UNDEFINED;

    /**
     * Flow name. One of ['implicit', 'password', 'authorizationCode', 'clientCredentials'].
     *
     * @var string
     */
    public $flow = Generator::UNDEFINED;

    /**
     * The available scopes for the OAuth2 security scheme. A map between the scope name and a short description for it.
     */
    public $scopes = Generator::UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_required = ['scopes', 'flow'];

    /**
     * @inheritdoc
     */
    public static $_blacklist = ['_context', '_unmerged'];

    /**
     * @inheritdoc
     */
    public static $_types = [
        'flow' => ['implicit', 'password', 'authorizationCode', 'clientCredentials'],
        'refreshUrl' => 'string',
        'authorizationUrl' => 'string',
        'tokenUrl' => 'string',
    ];

    /**
     * @inheritdoc
     */
    public static $_parents = [
        SecurityScheme::class,
    ];

    /**
     * @inheritdoc
     */
    public static $_nested = [
        Attachable::class => ['attachables'],
    ];

    /** @inheritdoc */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        if (is_array($this->scopes) && empty($this->scopes)) {
            $this->scopes = new \stdClass();
        }

        return parent::jsonSerialize();
    }
}

if (\PHP_VERSION_ID >= 80100) {
    /**
     * @Annotation
     */
    #[\Attribute(\Attribute::TARGET_CLASS)]
    class Flow extends AbstractFlow
    {
        public function __construct(
            array $properties = [],
            string $authorizationUrl = Generator::UNDEFINED,
            string $tokenUrl = Generator::UNDEFINED,
            string $refreshUrl = Generator::UNDEFINED,
            string $flow = Generator::UNDEFINED,
            ?array $scopes = null,
            ?array $x = null,
            ?array $attachables = null
        ) {
            parent::__construct($properties + [
                    'authorizationUrl' => $authorizationUrl,
                    'tokenUrl' => $tokenUrl,
                    'refreshUrl' => $refreshUrl,
                    'flow' => $flow,
                    'scopes' => $scopes ?? Generator::UNDEFINED,
                    'x' => $x ?? Generator::UNDEFINED,
                    'value' => $this->combine($attachables),
                ]);
        }
    }
} else {
    /**
     * @Annotation
     */
    class Flow extends AbstractFlow
    {
        public function __construct(array $properties)
        {
            parent::__construct($properties);
        }
    }
}
