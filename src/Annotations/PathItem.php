<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * Describes the operations available on a single path.
 *
 * A Path Item may be empty, due to ACL constraints.
 * The path itself is still exposed to the documentation viewer, but they will not know which operations and parameters are available.
 *
 * @see [OAI Path Item Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#path-item-object)
 *
 * @Annotation
 */
class PathItem extends AbstractAnnotation
{
    /**
     * @see [Using refs](https://swagger.io/docs/specification/using-ref/)
     *
     * @var string|class-string|object
     */
    public $ref = Generator::UNDEFINED;

    /**
     * An optional, string summary, intended to apply to all operations in this path.
     *
     * @var string
     */
    public $summary = Generator::UNDEFINED;

    /**
     * An optional, string description, intended to apply to all operations in this path.
     *
     * @var string
     */
    public $description = Generator::UNDEFINED;

    /**
     * Key for the Path Object (OpenApi->paths array).
     *
     * @var string
     */
    public $path = Generator::UNDEFINED;

    /**
     * A definition of a GET operation on this path.
     *
     * @var Get
     */
    public $get = Generator::UNDEFINED;

    /**
     * A definition of a PUT operation on this path.
     *
     * @var Put
     */
    public $put = Generator::UNDEFINED;

    /**
     * A definition of a POST operation on this path.
     *
     * @var Post
     */
    public $post = Generator::UNDEFINED;

    /**
     * A definition of a DELETE operation on this path.
     *
     * @var Delete
     */
    public $delete = Generator::UNDEFINED;

    /**
     * A definition of a OPTIONS operation on this path.
     *
     * @var Options
     */
    public $options = Generator::UNDEFINED;

    /**
     * A definition of a HEAD operation on this path.
     *
     * @var Head
     */
    public $head = Generator::UNDEFINED;

    /**
     * A definition of a PATCH operation on this path.
     *
     * @var Patch
     */
    public $patch = Generator::UNDEFINED;

    /**
     * A definition of a TRACE operation on this path.
     *
     * @var Trace
     */
    public $trace = Generator::UNDEFINED;

    /**
     * An alternative server array to service all operations in this path.
     *
     * @var Server[]
     */
    public $servers = Generator::UNDEFINED;

    /**
     * A list of parameters that are applicable for all the operations described under this path.
     *
     * These parameters can be overridden at the operation level, but cannot be removed there.
     * The list must not include duplicated parameters.
     * A unique parameter is defined by a combination of a name and location.
     * The list can use the Reference Object to link to parameters that are defined at the OpenAPI Object's components/parameters.
     *
     * @var Parameter[]
     */
    public $parameters = Generator::UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_types = [
        'path' => 'string',
        'summary' => 'string',
    ];

    /**
     * @inheritdoc
     */
    public static $_nested = [
        Get::class => 'get',
        Post::class => 'post',
        Put::class => 'put',
        Delete::class => 'delete',
        Patch::class => 'patch',
        Trace::class => 'trace',
        Head::class => 'head',
        Options::class => 'options',
        Parameter::class => ['parameters'],
        PathParameter::class => ['parameters'],
        Server::class => ['servers'],
        Attachable::class => ['attachables'],
    ];

    /**
     * @inheritdoc
     */
    public static $_parents = [
        OpenApi::class,
    ];
}
