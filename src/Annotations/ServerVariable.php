<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * An object representing a server variable for server URL template substitution.
 *
 * @see [OAI Server Variable Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#server-variable-object)
 *
 * @Annotation
 */
class ServerVariable extends AbstractAnnotation
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
     *
     * Unlike the Schema Object's default, this value must be provided by the consumer.
     *
     * @var string
     */
    public $default = Generator::UNDEFINED;

    /**
     * A map between a variable name and its value.
     *
     * The value is used for substitution in the server's URL template.
     *
     * @var array
     */
    public $variables = Generator::UNDEFINED;

    /**
     * An optional description for the server variable.
     *
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
