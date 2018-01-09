<?php

namespace Swagger\Annotations;

/**
 * Class Flow
 * Security scheme flow object.
 *
 * @package Swagger\Annotations
 *
 * @author  Donii Sergii <doniysa@gmail.com>
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
    public $authorizationUrl;

    /**
     * The authorization URL to be used this flow
     *
     * @var string
     */
    public $tokenUrl;

    /**
     * The token URL to be used this flow
     *
     * @var string
     */
    public $refreshUrl;

    /**
     * Flow name. One of ['implicit', 'password', 'authorizationCode', 'clientCredentials']
     *
     * @var string
     */
    public $flow;

    /**
     * Authorization scopes
     *
     * @var array
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public $scopes = [];

    /** @inheritdoc */
    public static $_required = ['scopes', 'flow'];

    /**
     * {@inheritdoc}
     */
    public static $_blacklist = ['_context', '_unmerged'];

    /** @inheritdoc */
    public static $_types = [
        'flow'             => ['implicit', 'password', 'authorizationCode', 'clientCredentials'],
        'refreshUrl'       => 'string',
        'authorizationUrl' => 'string',
        'tokenUrl'         => 'string',
    ];

    /** @inheritdoc */
    public static $_parents = [
        'Swagger\Annotations\SecurityScheme',
    ];
}
