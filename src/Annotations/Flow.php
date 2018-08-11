<?php declare(strict_types=1);

namespace OpenApi\Annotations;

/**
 * Security scheme flow object.
 *
 * @Annotation
 */
class Flow extends AbstractAnnotation
{
    /**
     * Authorization url
     *
     * @var string
     */
    public $authorizationUrl = UNDEFINED;

    /**
     * The authorization URL to be used this flow
     *
     * @var string
     */
    public $tokenUrl = UNDEFINED;

    /**
     * The token URL to be used this flow
     *
     * @var string
     */
    public $refreshUrl = UNDEFINED;

    /**
     * Flow name. One of ['implicit', 'password', 'authorizationCode', 'clientCredentials']
     *
     * @var string
     */
    public $flow = UNDEFINED;

    /**
     * Authorization scopes
     *
     * @var array
     *
     * @license Apache 2.0
     */
    public $scopes = [];

    /**
     * @inheritdoc
     */
    public static $_required = ['scopes', 'flow'];

    /**
     * {@inheritdoc}
     */
    public static $_blacklist = ['_context', '_unmerged'];

    /**
     * @inheritdoc
     */
    public static $_types = [
        'flow'             => ['implicit', 'password', 'authorizationCode', 'clientCredentials'],
        'refreshUrl'       => 'string',
        'authorizationUrl' => 'string',
        'tokenUrl'         => 'string',
    ];

    /**
     * @inheritdoc
     */
    public static $_parents = [
        'OpenApi\Annotations\SecurityScheme',
    ];
}
