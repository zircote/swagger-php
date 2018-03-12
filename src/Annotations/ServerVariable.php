<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace Swagger\Annotations;

/**
 * @Annotation
 * A Server Variable Object https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#server-variable-object
 * An object representing a Server Variable for server URL template substitution.
 */
class ServerVariable extends AbstractAnnotation
{
    /**
     * The key into Server->variables array.
     * @var string
     */
    public $serverVariable;

    /**
     * An enumeration of string values to be used if the substitution options are from a limited set.
     *
     * @var string[]
     */
    public $enum;

    /**
     * The default value to use for substitution, and to send, if an alternate value is not supplied.
     * Unlike the Schema Object's default, this value must be provided by the consumer.
     *
     * @var string
     */
    public $default;

    /**
     * A map between a variable name and its value.
     * The value is used for substitution in the server's URL template.
     *
     * @var array
     */
    public $variables;

    /**
     * An optional description for the server variable.
     * CommonMark syntax MAY be used for rich text representation.
     *
     * @var string
     */
    public $description;

    /** @inheritdoc */
    public static $_parents = [
        'Swagger\Annotations\Server'
    ];

    /** @inheritdoc */
    public static $_required = ['default'];

    /** @inheritdoc */
    public static $_types = [
        'default' => 'string',
        'description' => 'string',
    ];
}
