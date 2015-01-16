<?php

/**
 * @license  Apache 2.0
 */

namespace Swagger\Annotations;

/**
 * Base class for the @SWG\Get(),  @SWG\Post(),  @SWG\Put(),  @SWG\Delete()
 * 
 * A Swagger "Operation Object": https://github.com/swagger-api/swagger-spec/blob/master/versions/2.0.md#operationObject
 */
abstract class Operation extends SwaggerAnnotation {
    
    /**
     *
     * @var string key in the Swagger "Paths Object" for this operation
     */
    public $path;

    /**
     *
     * @var string key in the Swagger "Path Item Object" for this operation
     */
    public $method;

    /**
     * @var [string]    A list of tags for API documentation control. Tags can be used for logical grouping of operations by resources or any other qualifier.
     */
    public $tags;

    /**
     * @var string  A short summary of what the operation does. For maximum readability in the swagger-ui, this field SHOULD be less than 120 characters.
     */
    public $summary;

    /**
     * @var string  A verbose explanation of the operation behavior. GFM syntax can be used for rich text representation.
     */
    public $description;

    /**
     * @var External Documentation Object   Additional external documentation for this operation.
     */
    public $externalDocs;

    /**
     * @var string  A friendly name for the operation. The id MUST be unique among all operations described in the API. Tools and libraries MAY use the operation id to uniquely identify an operation.
     */
    public $operationId;

    /**
     * @var [string]    A list of MIME types the operation can consume. This overrides the [consumes](#swaggerConsumes) definition at the Swagger Object. An empty value MAY be used to clear the global definition. Value MUST be as described under Mime Types.
     */
    public $consumes;

    /**
     * @var [string]    A list of MIME types the operation can produce. This overrides the [produces](#swaggerProduces) definition at the Swagger Object. An empty value MAY be used to clear the global definition. Value MUST be as described under Mime Types.
     */
    public $produces;

    /**
     * @var [Parameter Object | Reference Object]   A list of parameters that are applicable for this operation. If a parameter is already defined at the Path Item, the new definition will override it, but can never remove it. The list MUST NOT include duplicated parameters. A unique parameter is defined by a combination of a name and location. The list can use the Reference Object to link to parameters that are defined at the Swagger Object's parameters. There can be one "body" parameter at most.
     */
    public $parameters;

    /**
     * @var Responses Object    Required. The list of possible responses as they are returned from executing this operation.
     */
    public $responses;

    /**
     * @var [string]    The transfer protocol for the operation. Values MUST be from the list: "http", "https", "ws", "wss". The value overrides the Swagger Object schemes definition.
     */
    public $schemes;

    /**
     * @var boolean Declares this operation to be deprecated. Usage of the declared operation should be refrained. Default value is false.
     */
    public $deprecated;

    /**
     * @var [Security Requirement Object]   A declaration of which security schemes are applied for this operation. The list of values describes alternative security schemes that can be used (that is, there is a logical OR between the security requirements). This definition overrides any declared top-level security. To remove a top-level security declaration, an empty array can be used.
     */
    public $security;

}
