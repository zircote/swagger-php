<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * A Components Object: https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#components-object.
 *
 * Holds a set of reusable objects for different aspects of the OA.
 * All objects defined within the components object will have no effect on the API unless they are explicitly referenced from properties outside the components object.
 *
 * @Annotation
 */
abstract class AbstractComponents extends AbstractAnnotation
{
    /**
     * Schema reference.
     *
     * @var string
     */
    const SCHEMA_REF = '#/components/schemas/';

    /**
     * Reusable Schemas.
     *
     * @var Schema[]
     */
    public $schemas = Generator::UNDEFINED;

    /**
     * Reusable Responses.
     *
     * @var Response[]
     */
    public $responses = Generator::UNDEFINED;

    /**
     * Reusable Parameters.
     *
     * @var Parameter[]
     */
    public $parameters = Generator::UNDEFINED;

    /**
     * Reusable Examples.
     *
     * @var Examples[]
     */
    public $examples = Generator::UNDEFINED;

    /**
     * Reusable Request Bodys.
     *
     * @var RequestBody[]
     */
    public $requestBodies = Generator::UNDEFINED;

    /**
     * Reusable Headers.
     *
     * @var Header[]
     */
    public $headers = Generator::UNDEFINED;

    /**
     * Reusable Security Schemes.
     *
     * @var SecurityScheme[]
     */
    public $securitySchemes = Generator::UNDEFINED;

    /**
     * Reusable Links.
     *
     * @var Link[]
     */
    public $links = Generator::UNDEFINED;

    /**
     * Reusable Callbacks.
     *
     * @var callable[]
     */
    public $callbacks = Generator::UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_parents = [
        OpenApi::class,
    ];

    /**
     * @inheritdoc
     */
    public static $_nested = [
        Schema::class => ['schemas', 'schema'],
        Response::class => ['responses', 'response'],
        Parameter::class => ['parameters', 'parameter'],
        PathParameter::class => ['parameters', 'parameter'],
        RequestBody::class => ['requestBodies', 'request'],
        Examples::class => ['examples', 'example'],
        Header::class => ['headers', 'header'],
        SecurityScheme::class => ['securitySchemes', 'securityScheme'],
        Link::class => ['links', 'link'],
        Attachable::class => ['attachables'],
    ];
}

if (\PHP_VERSION_ID >= 80100) {
    /**
     * @Annotation
     */
    #[\Attribute(\Attribute::TARGET_CLASS)]
    class Components extends AbstractComponents
    {
        public function __construct(
            array $properties = []
        ) {
            parent::__construct($properties + [
                ]);
        }
    }
} else {
    /**
     * @Annotation
     */
    class Components extends AbstractComponents
    {
        public function __construct(array $properties)
        {
            parent::__construct($properties);
        }
    }
}
