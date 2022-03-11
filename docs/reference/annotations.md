# Annotations

This page is generated automatically from the `swagger-php` sources.

For improvements head over to [GitHub](https://github.com/zircote/swagger-php) and create a PR ;)

In addition to this page, there are also a number of [examples](https://github.com/zircote/swagger-php/tree/master/Examples#readme) which might help you out.


## [AdditionalProperties](https://github.com/zircote/swagger-php/tree/master/src/Annotations/AdditionalProperties.php)

#### Properties
## [Attachable](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Attachable.php)

A container for custom data to be attached to an annotation.

These will be ignored by `swagger-php` but can be used for custom processing.
#### Properties
## [Components](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Components.php)

Holds a set of reusable objects for different aspects of the OA.

All objects defined within the components object will have no effect on the API unless they are explicitly
referenced from properties outside the components object.
#### Properties
- schemas
- responses
- parameters
- examples
- requestBodies
- headers
- securitySchemes
- links
- callbacks
#### Reference
- [OAI Components Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#components-object)
## [Contact](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Contact.php)

Contact information for the exposed API.
#### Properties
- name
- url
- email
#### Reference
- [OAI Contact Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#contact-object)
## [Delete](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Delete.php)

#### Properties
- method
## [Discriminator](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Discriminator.php)

The discriminator is a specific object in a schema which is used to inform the consumer of
the specification of an alternative schema based on the value associated with it.

This object is based on the [JSON Schema Specification](http://json-schema.org) and uses a predefined subset of it.
On top of this subset, there are extensions provided by this specification to allow for more complete documentation.
#### Properties
- propertyName
- mapping
#### Reference
- [OAI Discriminator Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#discriminatorObject)
- [JSON Schema](http://json-schema.org/)
## [Examples](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Examples.php)

#### Properties
- ref
- example
- summary
- description
- value
- externalValue
## [ExternalDocumentation](https://github.com/zircote/swagger-php/tree/master/src/Annotations/ExternalDocumentation.php)

Allows referencing an external resource for extended documentation.
#### Properties
- description
- url
#### Reference
- [OAI External Documentation Object](https://github.com/OAI/OpenAPI-Specification/blob/OpenAPI.next/versions/3.0.md#external-documentation-object)
## [Flow](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Flow.php)

Configuration details for a supported OAuth Flow.
#### Properties
- authorizationUrl
- tokenUrl
- refreshUrl
- flow
- scopes
#### Reference
- [OAI OAuth Flow Object](https://swagger.io/specification/#oauthFlowObject)
## [Get](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Get.php)

#### Properties
- method
## [Head](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Head.php)

#### Properties
- method
## [Header](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Header.php)

#### Properties
- ref
- header
- description
- required
- schema
- deprecated
- allowEmptyValue
#### Reference
- [OAI Header Object](https://github.com/OAI/OpenAPI-Specification/blob/OpenAPI.next/versions/3.0.md#headerObject).
## [Info](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Info.php)

The object provides metadata about the API.

The metadata may be used by the clients if needed, and may be presented in editing or documentation generation tools for convenience.
#### Properties
- title
- description
- termsOfService
- contact
- license
- version
#### Reference
- [OAI Info Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#info-object)
## [Items](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Items.php)

The description of an item in a Schema with type "array".
#### Properties
## [JsonContent](https://github.com/zircote/swagger-php/tree/master/src/Annotations/JsonContent.php)

Shorthand for a json response.

Use as `@OA\Schema` inside a `Response` and `MediaType`->`'application/json'` will be generated.
#### Properties
- examples
## [License](https://github.com/zircote/swagger-php/tree/master/src/Annotations/License.php)

License information for the exposed API.
#### Properties
- name
- identifier
- url
#### Reference
- [OAI License Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#license-object)
## [Link](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Link.php)

The Link object represents a possible design-time link for a response.

The presence of a link does not guarantee the caller's ability to successfully invoke it, rather it provides a known
relationship and traversal mechanism between responses and other operations.

Unlike dynamic links (i.e. links provided in the response payload), the OA linking mechanism does not require
link information in the runtime response.

For computing links, and providing instructions to execute them, a runtime expression is used for
accessing values in an operation and using them as parameters while invoking the linked operation.
#### Properties
- ref
- link
- operationRef
- operationId
- parameters
- requestBody
- description
- server
#### Reference
- [OAI Link Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#link-object)
## [MediaType](https://github.com/zircote/swagger-php/tree/master/src/Annotations/MediaType.php)

Each Media Type Object provides schema and examples for the media type identified by its key.
#### Properties
- mediaType
- schema
- example
- examples
- encoding
#### Reference
- [OAI Media Type Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#media-type-object)
## [OpenApi](https://github.com/zircote/swagger-php/tree/master/src/Annotations/OpenApi.php)

This is the root document object for the API specification.
#### Properties
- openapi
- info
- servers
- paths
- components
- security
- tags
- externalDocs
#### Reference
- [OAI OpenApi Object](https://github.com/OAI/OpenAPI-Specification/blob/OpenAPI.next/versions/3.0.md#openapi-object)
## [Options](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Options.php)

#### Properties
- method
## [Parameter](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Parameter.php)

Describes a single operation parameter.

A unique parameter is defined by a combination of a name and location.
#### Properties
- ref
- parameter
- name
- in
- description
- required
- deprecated
- allowEmptyValue
- style
- explode
- allowReserved
- schema
- example
- examples
- content
- matrix
- label
- form
- simple
- spaceDelimited
- pipeDelimited
- deepObject
#### Reference
- [OAA Parameter Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#parameter-object)
## [Patch](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Patch.php)

#### Properties
- method
## [PathItem](https://github.com/zircote/swagger-php/tree/master/src/Annotations/PathItem.php)

Describes the operations available on a single path.

A Path Item may be empty, due to ACL constraints.
The path itself is still exposed to the documentation viewer but they will not know which operations and parameters are available.
#### Properties
- ref
- path
- summary
- get
- put
- post
- delete
- options
- head
- patch
- trace
- servers
- parameters
#### Reference
- [OAI Path Item Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#path-item-object)
## [PathParameter](https://github.com/zircote/swagger-php/tree/master/src/Annotations/PathParameter.php)

A request path parameter.
#### Properties
- in
- required
## [Post](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Post.php)

#### Properties
- method
## [Property](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Property.php)

#### Properties
- property
## [Put](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Put.php)

#### Properties
- method
## [RequestBody](https://github.com/zircote/swagger-php/tree/master/src/Annotations/RequestBody.php)

Describes a single request body.
#### Properties
- ref
- request
- description
- required
- content
#### Reference
- [OAI Request Body Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#requestBodyObject)
## [Response](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Response.php)

Describes a single response from an API Operation, including design-time, static links to operations based on the
response.
#### Properties
- ref
- response
- description
- headers
- content
- links
#### Reference
- [OAI Response Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#response-object)
## [Schema](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Schema.php)

The definition of input and output data types.

These types can be objects, but also primitives and arrays.

This object is based on the [JSON Schema Specification](http://json-schema.org) and uses a predefined subset of it.
On top of this subset, there are extensions provided by this specification to allow for more complete documentation.
#### Properties
- ref
- schema
- title
- description
- maxProperties
- minProperties
- required
- properties
- type
- format
- items
- collectionFormat
- default
- maximum
- exclusiveMaximum
- minimum
- exclusiveMinimum
- maxLength
- minLength
- pattern
- maxItems
- minItems
- uniqueItems
- enum
- multipleOf
- discriminator
- readOnly
- writeOnly
- xml
- externalDocs
- example
- nullable
- deprecated
- allOf
- anyOf
- oneOf
- not
- additionalProperties
- additionalItems
- contains
- patternProperties
- dependencies
- propertyNames
- const
#### Reference
- [OAI Schema Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#schemaObject)
- [JSON Schema](http://json-schema.org/)
## [SecurityScheme](https://github.com/zircote/swagger-php/tree/master/src/Annotations/SecurityScheme.php)

#### Properties
- ref
- securityScheme
- type
- description
- name
- in
- flows
- bearerFormat
- scheme
- openIdConnectUrl
#### Reference
- [OAI Security Scheme Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#securitySchemeObject).
## [Server](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Server.php)

An object representing a Server.
#### Properties
- url
- description
- variables
#### Reference
- [OAI Server Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#server-object)
## [ServerVariable](https://github.com/zircote/swagger-php/tree/master/src/Annotations/ServerVariable.php)

An object representing a Server Variable for server URL template substitution.
#### Properties
- serverVariable
- enum
- default
- variables
- description
#### Reference
- [OAI Server Variable Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#server-variable-object)
## [Tag](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Tag.php)

#### Properties
- name
- description
- externalDocs
#### Reference
- [OAI Tag Object]( https://github.com/OAI/OpenAPI-Specification/blob/OpenAPI.next/versions/3.0.md#tagObject).
## [Trace](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Trace.php)

#### Properties
- method
## [Xml](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Xml.php)

#### Properties
- name
- namespace
- prefix
- attribute
- wrapped
#### Reference
- [OAI XML Object](https://github.com/OAI/OpenAPI-Specification/blob/OpenAPI.next/versions/3.0.md#xmlObject).
## [XmlContent](https://github.com/zircote/swagger-php/tree/master/src/Annotations/XmlContent.php)

Shorthand for a xml response.

Use as an Schema inside a Response and the MediaType "application/xml" will be generated.
#### Properties
- examples
