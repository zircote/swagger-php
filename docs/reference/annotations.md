# Annotations

This page is generated automatically from the `swagger-php` sources.

For improvements head over to [GitHub](https://github.com/zircote/swagger-php) and create a PR ;)

In addition to this page, there are also a number of [examples](https://github.com/zircote/swagger-php/tree/master/Examples#readme) which might help you out.


## [AdditionalProperties](https://github.com/zircote/swagger-php/tree/master/src/Annotations/AdditionalProperties.php)

### Properties
## [Attachable](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Attachable.php)

A container for custom data to be attached to an annotation.

These will be ignored by `swagger-php` but can be used for custom processing.
### Properties
## [Components](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Components.php)

Holds a set of reusable objects for different aspects of the OA.

All objects defined within the components object will have no effect on the API unless they are explicitly
referenced from properties outside the components object.

### References
- [OAI Components Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#components-object)
### Properties
- schemas
- responses
- parameters
- examples
- requestBodies
- headers
- securitySchemes
- links
- callbacks
## [Contact](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Contact.php)

Contact information for the exposed API.

### References
- [OAI Contact Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#contact-object)
### Properties
- name
- url
- email
## [Delete](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Delete.php)

### Properties
- method
## [Discriminator](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Discriminator.php)

The discriminator is a specific object in a schema which is used to inform the consumer of
the specification of an alternative schema based on the value associated with it.

This object is based on the [JSON Schema Specification](http://json-schema.org) and uses a predefined subset of it.
On top of this subset, there are extensions provided by this specification to allow for more complete documentation.

### References
- [OAI Discriminator Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#discriminatorObject)
- [JSON Schema](http://json-schema.org/)
### Properties
- propertyName
- mapping
## [Examples](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Examples.php)

### Properties
- ref
- example
- summary
- description
- value
- externalValue
## [ExternalDocumentation](https://github.com/zircote/swagger-php/tree/master/src/Annotations/ExternalDocumentation.php)

Allows referencing an external resource for extended documentation.

### References
- [OAI External Documentation Object](https://github.com/OAI/OpenAPI-Specification/blob/OpenAPI.next/versions/3.0.md#external-documentation-object)
### Properties
- description
- url
## [Flow](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Flow.php)

Configuration details for a supported OAuth Flow.

### References
- [OAI OAuth Flow Object](https://swagger.io/specification/#oauthFlowObject)
### Properties
- authorizationUrl
- tokenUrl
- refreshUrl
- flow
- scopes
## [Get](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Get.php)

### Properties
- method
## [Head](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Head.php)

### Properties
- method
## [Header](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Header.php)

### References
- [OAI Header Object](https://github.com/OAI/OpenAPI-Specification/blob/OpenAPI.next/versions/3.0.md#headerObject).
### Properties
- ref
- header
- description
- required
- schema
- deprecated
- allowEmptyValue
## [Info](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Info.php)

The object provides metadata about the API.

The metadata may be used by the clients if needed, and may be presented in editing or documentation generation tools for convenience.

### References
- [OAI Info Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#info-object)
### Properties
- title
- description
- termsOfService
- contact
- license
- version
## [Items](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Items.php)

The description of an item in a Schema with type "array".
### Properties
## [JsonContent](https://github.com/zircote/swagger-php/tree/master/src/Annotations/JsonContent.php)

Shorthand for a json response.

Use as an Schema inside a Response and the MediaType "application/json" will be generated.
### Properties
- examples
## [License](https://github.com/zircote/swagger-php/tree/master/src/Annotations/License.php)

License information for the exposed API.

### References
- [OAI License Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#license-object)
### Properties
- name
- identifier
- url
## [Link](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Link.php)

The Link object represents a possible design-time link for a response.

The presence of a link does not guarantee the caller's ability to successfully invoke it, rather it provides a known
relationship and traversal mechanism between responses and other operations.

Unlike dynamic links (i.e. links provided in the response payload), the OA linking mechanism does not require
link information in the runtime response.

For computing links, and providing instructions to execute them, a runtime expression is used for
accessing values in an operation and using them as parameters while invoking the linked operation.

### References
- [OAI Link Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#link-object)
### Properties
- ref
- link
- operationRef
- operationId
- parameters
- requestBody
- description
- server
## [MediaType](https://github.com/zircote/swagger-php/tree/master/src/Annotations/MediaType.php)

Each Media Type Object provides schema and examples for the media type identified by its key.

### References
- [OAI Media Type Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#media-type-object)
### Properties
- mediaType
- schema
- example
- examples
- encoding
## [OpenApi](https://github.com/zircote/swagger-php/tree/master/src/Annotations/OpenApi.php)

This is the root document object for the API specification.

### References
- [OAI OpenApi Object](https://github.com/OAI/OpenAPI-Specification/blob/OpenAPI.next/versions/3.0.md#openapi-object)
### Properties
- openapi
- info
- servers
- paths
- components
- security
- tags
- externalDocs
## [Options](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Options.php)

### Properties
- method
## [Parameter](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Parameter.php)

Describes a single operation parameter.

A unique parameter is defined by a combination of a name and location.

### References
- [OAA Parameter Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#parameter-object)
### Properties
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
## [Patch](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Patch.php)

### Properties
- method
## [PathItem](https://github.com/zircote/swagger-php/tree/master/src/Annotations/PathItem.php)

Describes the operations available on a single path.

A Path Item may be empty, due to ACL constraints.
The path itself is still exposed to the documentation viewer but they will not know which operations and parameters are available.

### References
- [OAI Path Item Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#path-item-object)
### Properties
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
## [PathParameter](https://github.com/zircote/swagger-php/tree/master/src/Annotations/PathParameter.php)

A request path parameter.
### Properties
- in
- required
## [Post](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Post.php)

### Properties
- method
## [Property](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Property.php)

### Properties
- property
## [Put](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Put.php)

### Properties
- method
## [RequestBody](https://github.com/zircote/swagger-php/tree/master/src/Annotations/RequestBody.php)

Describes a single request body.

### References
- [OAI Request Body Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#requestBodyObject)
### Properties
- ref
- request
- description
- required
- content
## [Response](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Response.php)

Describes a single response from an API Operation, including design-time, static links to operations based on the
response.

### References
- [OAI Response Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#response-object)
### Properties
- ref
- response
- description
- headers
- content
- links
## [Schema](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Schema.php)

The definition of input and output data types.

These types can be objects, but also primitives and arrays.

This object is based on the [JSON Schema Specification](http://json-schema.org) and uses a predefined subset of it.
On top of this subset, there are extensions provided by this specification to allow for more complete documentation.

### References
- [OAI Schema Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#schemaObject)
- [JSON Schema](http://json-schema.org/)
### Properties
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
## [SecurityScheme](https://github.com/zircote/swagger-php/tree/master/src/Annotations/SecurityScheme.php)

### References
- [OAI Security Scheme Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#securitySchemeObject).
### Properties
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
## [Server](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Server.php)

An object representing a Server.

### References
- [OAI Server Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#server-object)
### Properties
- url
- description
- variables
## [ServerVariable](https://github.com/zircote/swagger-php/tree/master/src/Annotations/ServerVariable.php)

An object representing a Server Variable for server URL template substitution.

### References
- [OAI Server Variable Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#server-variable-object)
### Properties
- serverVariable
- enum
- default
- variables
- description
## [Tag](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Tag.php)

### References
- [OAI Tag Object]( https://github.com/OAI/OpenAPI-Specification/blob/OpenAPI.next/versions/3.0.md#tagObject).
### Properties
- name
- description
- externalDocs
## [Trace](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Trace.php)

### Properties
- method
## [Xml](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Xml.php)

### References
- [OAI XML Object](https://github.com/OAI/OpenAPI-Specification/blob/OpenAPI.next/versions/3.0.md#xmlObject).
### Properties
- name
- namespace
- prefix
- attribute
- wrapped
## [XmlContent](https://github.com/zircote/swagger-php/tree/master/src/Annotations/XmlContent.php)

Shorthand for a xml response.

Use as an Schema inside a Response and the MediaType "application/xml" will be generated.
### Properties
- examples
