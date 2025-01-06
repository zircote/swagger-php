<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * The Link object represents a possible design-time link for a response.
 *
 * The presence of a link does not guarantee the caller's ability to successfully invoke it, rather it provides a known
 * relationship and traversal mechanism between responses and other operations.
 *
 * Unlike dynamic links (i.e. links provided in the response payload), the OA linking mechanism does not require
 * link information in the runtime response.
 *
 * For computing links, and providing instructions to execute them, a runtime expression is used for
 * accessing values in an operation and using them as parameters while invoking the linked operation.
 *
 * @see [Link Object](https://spec.openapis.org/oas/v3.1.1.html#link-object)
 *
 * @Annotation
 */
class Link extends AbstractAnnotation
{
    /**
     * @see [Reference Object](https://spec.openapis.org/oas/v3.1.1.html#reference-object)
     *
     * @var string|class-string|object
     */
    public $ref = Generator::UNDEFINED;

    /**
     * The key into MediaType->links array.
     *
     * @var string
     */
    public $link = Generator::UNDEFINED;

    /**
     * A relative or absolute reference to an OA operation.
     *
     * This field is mutually exclusive of the <code>operationId</code> field, and must point to an Operation object.
     *
     * Relative values may be used to locate an existing Operation object in the OpenAPI definition.
     *
     * @var string
     */
    public $operationRef = Generator::UNDEFINED;

    /**
     * The name of an existing, resolvable OA operation, as defined with a unique <code>operationId</code>.
     *
     * This field is mutually exclusive of the <code>operationRef</code> field.
     *
     * @var string
     */
    public $operationId = Generator::UNDEFINED;

    /**
     * A map representing parameters to pass to an operation as specified with operationId or identified via
     * operationRef.
     *
     * The key is the parameter name to be used, whereas the value can be a constant or an expression to
     * be evaluated and passed to the linked operation.
     * The parameter name can be qualified using the parameter location [{in}.]{name} for operations
     * that use the same parameter name in different locations (e.g. path.id).
     *
     * @var array<string,mixed>
     */
    public $parameters = Generator::UNDEFINED;

    /**
     * A literal value or {expression} to use as a request body when calling the target operation.
     */
    public $requestBody = Generator::UNDEFINED;

    /**
     * A description of the link.
     *
     * CommonMark syntax may be used for rich text representation.
     *
     * @var string
     */
    public $description = Generator::UNDEFINED;

    /**
     * A server object to be used by the target operation.
     *
     * @var Server
     */
    public $server = Generator::UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_nested = [
        Server::class => 'server',
        Attachable::class => ['attachables'],
    ];

    /**
     * @inheritdoc
     */
    public static $_parents = [
        Components::class,
        Response::class,
    ];
}
