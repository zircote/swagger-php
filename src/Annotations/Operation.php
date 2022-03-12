<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * Base class for `@OA\Get`,  `@OA\Post`,  `@OA\Put`,  etc.
 *
 * Describes a single API operation on a path.
 *
 * @see [OAI Operation Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#operation-object)
 *
 * @Annotation
 */
abstract class Operation extends AbstractAnnotation
{
    /**
     * key in the OpenApi "Paths Object" for this operation.
     *
     * @var string
     */
    public $path = Generator::UNDEFINED;

    /**
     * A list of tags for API documentation control.
     *
     * Tags can be used for logical grouping of operations by resources or any other qualifier.
     *
     * @var string[]
     */
    public $tags = Generator::UNDEFINED;

    /**
     * Key in the OpenApi "Path Item Object" for this operation.
     *
     * Allowed values: 'get', 'post', put', 'patch', 'delete', 'options', 'head' and 'trace'.
     *
     * @var string
     */
    public $method = Generator::UNDEFINED;

    /**
     * A short summary of what the operation does.
     *
     * @var string
     */
    public $summary = Generator::UNDEFINED;

    /**
     * A verbose explanation of the operation behavior.
     *
     * CommonMark syntax MAY be used for rich text representation.
     *
     * @var string
     */
    public $description = Generator::UNDEFINED;

    /**
     * Additional external documentation for this operation.
     *
     * @var ExternalDocumentation
     */
    public $externalDocs = Generator::UNDEFINED;

    /**
     * Unique string used to identify the operation.
     *
     * The id must be unique among all operations described in the API.
     * Tools and libraries may use the operationId to uniquely identify an operation, therefore, it is recommended to
     * follow common programming naming conventions.
     *
     * @var string
     */
    public $operationId = Generator::UNDEFINED;

    /**
     * A list of parameters that are applicable for this operation.
     *
     * If a parameter is already defined at the Path Item, the new definition will override it but can never remove it.
     * The list must not include duplicated parameters.
     *
     * A unique parameter is defined by a combination of a name and location.
     *
     * The list can use the Reference Object to link to parameters that are defined at the OpenAPI Object's
     * components/parameters.
     *
     * @var Parameter[]
     */
    public $parameters = Generator::UNDEFINED;

    /**
     * The request body applicable for this operation.
     *
     * The requestBody is only supported in HTTP methods where the HTTP 1.1 specification RFC7231 has explicitly
     * defined semantics for request bodies. In other cases where the HTTP spec is vague, requestBody shall be ignored
     * by consumers.
     *
     * @var RequestBody
     */
    public $requestBody = Generator::UNDEFINED;

    /**
     * The list of possible responses as they are returned from executing this operation.
     *
     * @var \OpenApi\Annotations\Response[]
     */
    public $responses = Generator::UNDEFINED;

    /**
     * A map of possible out-of band callbacks related to the parent operation.
     *
     * The key is a unique identifier for the Callback Object.
     *
     * Each value in the map is a Callback Object that describes a request that may be initiated by the API provider
     * and the expected responses. The key value used to identify the callback object is an expression, evaluated at
     * runtime, that identifies a URL to use for the callback operation.
     *
     * @var callable[]
     */
    public $callbacks = Generator::UNDEFINED;

    /**
     * Declares this operation to be deprecated.
     *
     * Consumers should refrain from usage of the declared operation.
     *
     * Default value is false.
     *
     * @var bool
     */
    public $deprecated = Generator::UNDEFINED;

    /**
     * A declaration of which security mechanisms can be used for this operation.
     *
     * The list of values includes alternative security requirement objects that can be used.
     *
     * Only one of the security requirement objects need to be satisfied to authorize a request.
     *
     * This definition overrides any declared top-level security.
     * To remove a top-level security declaration, an empty array can be used.
     *
     * @var array
     */
    public $security = Generator::UNDEFINED;

    /**
     * An alternative server array to service this operation.
     *
     * If an alternative server object is specified at the Path Item Object or Root level, it will be overridden by
     * this value.
     *
     * @var Server[]
     */
    public $servers = Generator::UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_required = ['responses'];

    /**
     * @inheritdoc
     */
    public static $_types = [
        'path' => 'string',
        'method' => 'string',
        'tags' => '[string]',
        'summary' => 'string',
        'description' => 'string',
        'deprecated' => 'boolean',
    ];

    /**
     * @inheritdoc
     */
    public static $_nested = [
        Parameter::class => ['parameters'],
        PathParameter::class => ['parameters'],
        Response::class => ['responses', 'response'],
        ExternalDocumentation::class => 'externalDocs',
        Server::class => ['servers'],
        RequestBody::class => 'requestBody',
        Attachable::class => ['attachables'],
    ];

    /**
     * @inheritdoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();

        unset($data->method);
        unset($data->path);

        // ensure security elements are object
        if (isset($data->security) && is_array($data->security)) {
            foreach ($data->security as $key => $scheme) {
                $data->security[$key] = (object) $scheme;
            }
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function validate(array $stack = [], array $skip = [], string $ref = '', $context = null): bool
    {
        if (in_array($this, $skip, true)) {
            return true;
        }

        $valid = parent::validate($stack, $skip, $ref, $context);

        if (!Generator::isDefault($this->responses)) {
            foreach ($this->responses as $response) {
                if (!Generator::isDefault($response->response) && $response->response !== 'default' && preg_match('/^([12345]{1}[0-9]{2})|([12345]{1}XX)$/', (string) $response->response) === 0) {
                    $this->_context->logger->warning('Invalid value "' . $response->response . '" for ' . $response->_identity([]) . '->response, expecting "default", a HTTP Status Code or HTTP Status Code range definition in ' . $response->_context);
                    $valid = false;
                }
            }
        }

        if (is_object($context) && !Generator::isDefault($this->operationId)) {
            if (!property_exists($context, 'operationIds')) {
                $context->operationIds = [];
            }

            if (in_array($this->operationId, $context->operationIds)) {
                $this->_context->logger->warning('operationId must be unique. Duplicate value found: "' . $this->operationId . '"');
                $valid = false;
            }

            $context->operationIds[] = $this->operationId;
        }

        return $valid;
    }
}
