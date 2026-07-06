<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Undefined;

/**
 * An object representing a server variable for server URL template substitution.
 *
 * @see [Server Variable Object](https://spec.openapis.org/oas/v3.1.1.html#server-variable-object)
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
    public $serverVariable = Undefined::UNDEFINED;

    /**
     * An enumeration of values to be used if the substitution options are from a limited set.
     *
     * @var list<string|int|float|bool|\UnitEnum>|class-string
     */
    public $enum = Undefined::UNDEFINED;

    /**
     * The default value to use for substitution, and to send, if an alternate value is not supplied.
     *
     * Unlike the Schema Object's default, this value must be provided by the consumer.
     *
     * @var string
     */
    public $default = Undefined::UNDEFINED;

    /**
     * A map between a variable name and its value.
     *
     * The value is used for substitution in the server's URL template.
     *
     * @var array
     */
    public $variables = Undefined::UNDEFINED;

    /**
     * An optional description for the server variable.
     *
     * CommonMark syntax MAY be used for rich text representation.
     *
     * @var string
     */
    public $description = Undefined::UNDEFINED;

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
