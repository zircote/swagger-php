<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * A Server Object https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#server-object.
 *
 * An object representing a Server.
 *
 * @Annotation
 */
abstract class AbstractServer extends AbstractAnnotation
{
    /**
     * A URL to the target host. This URL supports Server Variables and may be relative,
     * to indicate that the host location is relative to the location where the OpenAPI document is being served.
     * Variable substitutions will be made when a variable is named in {brackets}.
     *
     * @var string
     */
    public $url = Generator::UNDEFINED;

    /**
     * An optional string describing the host designated by the URL.
     * CommonMark syntax may be used for rich text representation.
     *
     * @var string
     */
    public $description = Generator::UNDEFINED;

    /**
     * A map between a variable name and its value.
     * The value is used for substitution in the server's URL template.
     *
     * @var array
     */
    public $variables = Generator::UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_parents = [
        OpenApi::class,
        PathItem::class,
        Operation::class,
        Get::class,
        Post::class,
        Put::class,
        Delete::class,
        Patch::class,
        Head::class,
        Options::class,
        Trace::class,
        Link::class,
    ];

    /**
     * @inheritdoc
     */
    public static $_nested = [
        ServerVariable::class => ['variables', 'serverVariable'],
        Attachable::class => ['attachables'],
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

if (\PHP_VERSION_ID >= 80100) {
    /**
     * @Annotation
     */
    #[\Attribute(\Attribute::TARGET_CLASS)]
    class Server extends AbstractServer
    {
        public function __construct(
            array $properties = [],
            string $url = Generator::UNDEFINED,
            string $description = Generator::UNDEFINED,
            ?array $variables = null,
            ?array $x = null,
            ?array $attachables = null
        ) {
            parent::__construct($properties + [
                    'url' => $url,
                    'description' => $description,
                    'x' => $x ?? Generator::UNDEFINED,
                    'value' => $this->combine($variables, $attachables),
                ]);
        }
    }
} else {
    /**
     * @Annotation
     */
    class Server extends AbstractServer
    {
        public function __construct(array $properties)
        {
            parent::__construct($properties);
        }
    }
}
