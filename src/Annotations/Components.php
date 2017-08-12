<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace Swagger\Annotations;

/**
 * @Annotation
 * A Components Object: https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#components-object
 * Holds a set of reusable objects for different aspects of the OAS.
 * All objects defined within the components object will have no effect on the API unless they are explicitly referenced from properties outside the components object.
 */
class Components extends AbstractAnnotation
{
    /**
     * Reusable Schemas.
     *
     * @var Schema[]
     */
    public $schemas;

    /**
     * Reusable Responses.
     *
     * @var Response[]
     */
    public $responses;

    /**
     * Reusable Parameters.
     *
     * @var Parameter[]
     */
    public $parameters;

    /**
     * Reusable Examples.
     *
     * @var Example[]
     */
    public $examples;

    /**
     * Reusable Request Bodys.
     *
     * @var Request[]
     */
    public $requestBodies;

    /**
     * Reusable Headers.
     *
     * @var Header[]
     */
    public $headers;

    /**
     * Reusable Security Schemes.
     *
     * @var SecurityScheme[]
     */
    public $securitySchemes;

    /**
     * Reusable Links.
     *
     * @var Link[]
     */
    public $links;

    /**
     * Reusable Callbacks.
     *
     * @var Callback[]
     */
    public $callbacks;

    /** @inheritdoc */
    public static $_parents = [
        'Swagger\Annotations\OpenApi'
    ];

    /** @inheritdoc */
    public static $_nested = [
        'Swagger\Annotations\Schema' => ['schemas', 'schema'],
        'Swagger\Annotations\Response' => ['responses', 'response'],
        'Swagger\Annotations\Parameter' => ['parameters', 'parameter'],
        // 'Swagger\Annotations\Request' => ['requestBodys', 'request'],
        'Swagger\Annotations\Header' => ['headers', 'header'],
        'Swagger\Annotations\SecurityScheme' => ['securitySchemes', 'securityScheme'],
    ];
}
