<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * A Server Variable Object https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#server-variable-object.
 *
 * An object representing a Server Variable for server URL template substitution.
 *
 * @Annotation
 */
abstract class AbstractServerVariable extends AbstractAnnotation
{
    /**
     * The key into Server->variables array.
     *
     * @var string
     */
    public $serverVariable = Generator::UNDEFINED;

    /**
     * An enumeration of string values to be used if the substitution options are from a limited set.
     *
     * @var string[]
     */
    public $enum = Generator::UNDEFINED;

    /**
     * The default value to use for substitution, and to send, if an alternate value is not supplied.
     * Unlike the Schema Object's default, this value must be provided by the consumer.
     *
     * @var string
     */
    public $default = Generator::UNDEFINED;

    /**
     * A map between a variable name and its value.
     * The value is used for substitution in the server's URL template.
     *
     * @var array
     */
    public $variables = Generator::UNDEFINED;

    /**
     * An optional description for the server variable.
     * CommonMark syntax MAY be used for rich text representation.
     *
     * @var string
     */
    public $description = Generator::UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_parents = [
        Server::class,
    ];

    /**
     * @inheritdoc
     */
    public static $_required = ['default'];

    /**
     * @inheritdoc
     */
    public static $_types = [
        'default' => 'string',
        'description' => 'string',
    ];

    /**
     * @inheritdoc
     */
    public static $_nested = [
        Attachable::class => ['attachables'],
    ];
}

if (\PHP_VERSION_ID >= 80100) {
    /**
     * @Annotation
     */
    #[\Attribute(\Attribute::TARGET_CLASS)]
    class ServerVariable extends AbstractServerVariable
    {
        public function __construct(
            array $properties = [],
            string $serverVariable = Generator::UNDEFINED,
            string $description = Generator::UNDEFINED,
            string $default = Generator::UNDEFINED,
            ?array $enum = null,
            ?array $variables = null,
            ?array $x = null,
            ?array $attachables = null
        ) {
            parent::__construct($properties + [
                    'serverVariable' => $serverVariable,
                    'description' => $description,
                    'default' => $default,
                    'enum' => $enum ?? Generator::UNDEFINED,
                    'variables' => $variables ?? Generator::UNDEFINED,
                    'x' => $x ?? Generator::UNDEFINED,
                    'value' => $this->combine($attachables),
                ]);
        }
    }
} else {
    /**
     * @Annotation
     */
    class ServerVariable extends AbstractServerVariable
    {
        public function __construct(array $properties)
        {
            parent::__construct($properties);
        }
    }
}
