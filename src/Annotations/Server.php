<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

/**
 * @Annotation
 * A Server Object https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#server-object
 * An object representing a Server.
 */
class Server extends AbstractAnnotation
{
    /**
     * A URL to the target host. This URL supports Server Variables and may be relative,
     * to indicate that the host location is relative to the location where the OpenAPI document is being served.
     * Variable substitutions will be made when a variable is named in {brackets}.
     *
     * @var string
     */
    public $url = UNDEFINED;

    /**
     * An optional string describing the host designated by the URL.
     * CommonMark syntax may be used for rich text representation.
     *
     * @var string
     */
    public $description = UNDEFINED;

    /**
     * A map between a variable name and its value.
     * The value is used for substitution in the server's URL template.
     *
     * @var array
     */
    public $variables = UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_parents = [
        'OpenApi\Annotations\OpenApi',
        'OpenApi\Annotations\PathItem',
        'OpenApi\Annotations\Operation',
        'OpenApi\Annotations\Get',
        'OpenApi\Annotations\Post',
        'OpenApi\Annotations\Put',
        'OpenApi\Annotations\Delete',
        'OpenApi\Annotations\Patch',
        'OpenApi\Annotations\Head',
        'OpenApi\Annotations\Options',
        'OpenApi\Annotations\Trace',
        'OpenApi\Annotations\Link',
    ];

    /**
     * @inheritdoc
     */
    public static $_nested = [
        'OpenApi\Annotations\ServerVariable' => ['variables', 'serverVariable'],
    ];

    /**
     * @inheritdoc
     */
    public static $_required = ['url'];

    /**
     * @inheritdoc
     */
    public static $_types = [
        'url' => 'string',
        'description' => 'string',
    ];
}
