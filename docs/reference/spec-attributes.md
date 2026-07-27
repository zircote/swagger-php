# Spec Attribute Reference

This page is generated automatically from the `swagger-php` sources.

For improvements head over to [GitHub](https://github.com/zircote/swagger-php) and create a PR ;)


Spec attributes are typed PHP 8.1+ attributes in the `OpenApi\Spec` namespace — the foundation of the
spec-attributes pipeline (`--mode spec` or `--mode hybrid`).

They are immutable data containers with no serialization logic. Relationships between attributes are
declared via `contains()` (what children an attribute absorbs) and `merge()` (what parent an attribute
composes into). The [Assembler](/reference/builder.md) resolves nesting, and [Augmenters](/reference/augmenters.md)
enrich the collected specification before compilation.

Typed subclasses (e.g. `Operation\Get`, `Parameter\Path`, `Flow\AuthorizationCode`) pre-fill common
fields to reduce boilerplate — the base class can always be used directly.

## Spec Attributes

### [Attachable](https://github.com/zircote/swagger-php/tree/master/src/Spec/Attachable.php)

Base class for custom attributes.

By default not allowed to contain other attributes, but can be inline nested into any other attribute
(including itself).

### [Components](https://github.com/zircote/swagger-php/tree/master/src/Spec/Components.php)

Container for reusable component definitions.

Place on a class to declare standalone components that go into the components section
of the OpenAPI document. The Components attribute itself is not emitted — its children
are promoted to their respective Specification buckets.

The primary use case is for DTOs that are NOT roots and therefore cannot be declared
at class level on their own: Parameter, Header, Link, and Example. Other types
(Schema, PathItem, SecurityScheme, named Response/RequestBody) are already roots and
can be declared directly on a class without needing a Components wrapper.

  #[Components]
  class SharedComponents {
      #[Parameter(parameter: 'tenant_id', name: 'tenant_id', in: 'path', schema: new Schema(type: 'string'))]
      public string $tenantId;

      #[Header(header: 'X-Rate-Limit', schema: new Schema(type: 'integer'))]
      public string $rateLimit;

      #[Example(example: 'dog', summary: 'A dog', value: ['name' => 'Fido'])]
      public string $dogExample;
  }

#### Nested elements
---
<a href="#schema">Schema</a>, <a href="#parameter">Parameter</a>, <a href="#response">Response</a>, <a href="#requestbody">RequestBody</a>, <a href="#header">Header</a>, <a href="#security-scheme">Security\Scheme</a>, <a href="#link">Link</a>, <a href="#example">Example</a>, <a href="#pathitem">PathItem</a>

### [Contact](https://github.com/zircote/swagger-php/tree/master/src/Spec/Contact.php)

Contact information for the exposed API.

#### Allowed in
---
<a href="#info">Info</a>

#### Parameters
---
- **name** : `string|null`  
  The identifying name of the contact person/organization
- **url** : `string|null`  
  A URL pointing to the contact information
- **email** : `string|null`  
  The email address of the contact person/organization

#### Reference
---
- [Contact Object](https://spec.openapis.org/oas/v3.1.1.html#contact-object) ↗

### [Discriminator](https://github.com/zircote/swagger-php/tree/master/src/Spec/Discriminator.php)

Aids in serialization, deserialization, and validation when request bodies or responses
can be one of several schemas (used with oneOf, anyOf, allOf).

#### Allowed in
---
<a href="#schema">Schema</a>

#### Parameters
---
- **propertyName** : `string|null`  
  The name of the property in the payload that distinguishes types
- **mapping** : `array&lt;string,string&gt;|null`  
  Maps payload values to schema names or references

#### Reference
---
- [Discriminator Object](https://spec.openapis.org/oas/v3.1.1.html#discriminator-object) ↗

### [Encoding](https://github.com/zircote/swagger-php/tree/master/src/Spec/Encoding.php)

Describes the encoding for a single property in a media type.

#### Allowed in
---
<a href="#mediatype">MediaType</a>

#### Parameters
---
- **encoding** : `string|null`  
  The property name this encoding applies to
- **contentType** : `string|null`  
  The Content-Type for encoding a specific property
- **headers** : `list&lt;Header&gt;|null`  
  Additional headers for multipart media types
- **style** : `string|null`  
  How the property value is serialized
- **explode** : `bool|null`  
  Whether arrays/objects generate separate parameters
- **allowReserved** : `bool|null`  
  Whether reserved characters are allowed without encoding

#### Reference
---
- [Encoding Object](https://spec.openapis.org/oas/v3.1.1.html#encoding-object) ↗

### [Example](https://github.com/zircote/swagger-php/tree/master/src/Spec/Example.php)

Describes an example value for a parameter, media type, or schema.

#### Allowed in
---
<a href="#components">Components</a>, <a href="#header">Header</a>, <a href="#mediatype">MediaType</a>, <a href="#parameter">Parameter</a>, <a href="#parameter-cookie">Parameter\Cookie</a>, <a href="#parameter-header">Parameter\Header</a>, <a href="#parameter-path">Parameter\Path</a>, <a href="#parameter-query">Parameter\Query</a>

#### Parameters
---
- **example** : `string|null`  
  Reusable example identifier (component key)
- **summary** : `string|null`  
  Short description of the example
- **description** : `string|null`  
  Long description of the example (CommonMark syntax)
- **value** : `mixed`  
  Embedded literal example value
- **externalValue** : `string|null`  
  A URI pointing to the literal example
- **ref** : `string|null`  
  A JSON Reference to a reusable example

#### Reference
---
- [Example Object](https://spec.openapis.org/oas/v3.1.1.html#example-object) ↗

### [ExternalDocumentation](https://github.com/zircote/swagger-php/tree/master/src/Spec/ExternalDocumentation.php)

Allows referencing an external resource for extended documentation.

#### Allowed in
---
<a href="#tag">Tag</a>

#### Parameters
---
- **url** : `string|null`  
  The URL for the target documentation
- **description** : `string|null`  
  A description of the target documentation (CommonMark syntax)

#### Reference
---
- [External Documentation Object](https://spec.openapis.org/oas/v3.1.1.html#external-documentation-object) ↗

### [Flow](https://github.com/zircote/swagger-php/tree/master/src/Spec/Flow.php)

Configuration details for a supported OAuth2 flow.

Typed subtypes pre-fill the flow type:
- `OA\Flow\Implicit` - implicit grant (authorizationUrl required)
- `OA\Flow\Password` - resource owner password credentials (tokenUrl required)
- `OA\Flow\ClientCredentials` - client credentials grant (tokenUrl required)
- `OA\Flow\AuthorizationCode` - authorization code grant (authorizationUrl + tokenUrl required)

  #[OA\Security\Scheme\OAuth2(securityScheme: 'oauth2', flows: [
      new OA\Flow\AuthorizationCode(
          authorizationUrl: 'https://example.com/oauth/authorize',
          tokenUrl: 'https://example.com/oauth/token',
          scopes: ['read:pets' => 'Read pets', 'write:pets' => 'Write pets'],
      ),
  ])]

Produces:
  components:
    securitySchemes:
      oauth2:
        type: oauth2
        flows:
          authorizationCode:
            authorizationUrl: https://example.com/oauth/authorize
            tokenUrl: https://example.com/oauth/token
            scopes:
              read:pets: Read pets
              write:pets: Write pets

#### Allowed in
---
<a href="#security-scheme">Security\Scheme</a>, <a href="#security-scheme-apikey">Security\Scheme\ApiKey</a>, <a href="#security-scheme-http">Security\Scheme\Http</a>, <a href="#security-scheme-mutualtls">Security\Scheme\MutualTls</a>, <a href="#security-scheme-oauth2">Security\Scheme\OAuth2</a>, <a href="#security-scheme-openidconnect">Security\Scheme\OpenIdConnect</a>

#### Parameters
---
- **flow** : `string|null`  
  The OAuth2 flow type (implicit, password, clientCredentials, authorizationCode)
- **authorizationUrl** : `string|null`  
  The authorization URL for this flow
- **tokenUrl** : `string|null`  
  The token URL for this flow
- **refreshUrl** : `string|null`  
  The URL for obtaining refresh tokens
- **scopes** : `array&lt;string,string&gt;|null`  
  The available scopes for the OAuth2 security scheme

#### Reference
---
- [OAuth Flow Object](https://spec.openapis.org/oas/v3.1.1.html#oauth-flow-object) ↗
- [OAuth Flows Object](https://spec.openapis.org/oas/v3.1.1.html#oauth-flows-object) ↗

### [Flow\AuthorizationCode](https://github.com/zircote/swagger-php/tree/master/src/Spec/Flow/AuthorizationCode.php)

Configuration for the OAuth2 Authorization Code flow.

#### Allowed in
---
<a href="#security-scheme">Security\Scheme</a>

#### Parameters
---
- **authorizationUrl** : `string|null`  
  No details available.
- **tokenUrl** : `string|null`  
  No details available.
- **refreshUrl** : `string|null`  
  No details available.
- **scopes** : `array&lt;string,string&gt;|null`  
  No details available.

#### Reference
---
- [OAuth Flow Object](https://spec.openapis.org/oas/v3.1.1.html#oauth-flow-object) ↗

### [Flow\ClientCredentials](https://github.com/zircote/swagger-php/tree/master/src/Spec/Flow/ClientCredentials.php)

Configuration for the OAuth2 Client Credentials flow.

#### Allowed in
---
<a href="#security-scheme">Security\Scheme</a>

#### Parameters
---
- **tokenUrl** : `string|null`  
  No details available.
- **refreshUrl** : `string|null`  
  No details available.
- **scopes** : `array&lt;string,string&gt;|null`  
  No details available.

#### Reference
---
- [OAuth Flow Object](https://spec.openapis.org/oas/v3.1.1.html#oauth-flow-object) ↗

### [Flow\Implicit](https://github.com/zircote/swagger-php/tree/master/src/Spec/Flow/Implicit.php)

Configuration for the OAuth2 Implicit flow.

#### Allowed in
---
<a href="#security-scheme">Security\Scheme</a>

#### Parameters
---
- **authorizationUrl** : `string|null`  
  No details available.
- **refreshUrl** : `string|null`  
  No details available.
- **scopes** : `array&lt;string,string&gt;|null`  
  No details available.

#### Reference
---
- [OAuth Flow Object](https://spec.openapis.org/oas/v3.1.1.html#oauth-flow-object) ↗

### [Flow\Password](https://github.com/zircote/swagger-php/tree/master/src/Spec/Flow/Password.php)

Configuration for the OAuth2 Resource Owner Password flow.

#### Allowed in
---
<a href="#security-scheme">Security\Scheme</a>

#### Parameters
---
- **tokenUrl** : `string|null`  
  No details available.
- **refreshUrl** : `string|null`  
  No details available.
- **scopes** : `array&lt;string,string&gt;|null`  
  No details available.

#### Reference
---
- [OAuth Flow Object](https://spec.openapis.org/oas/v3.1.1.html#oauth-flow-object) ↗

### [Header](https://github.com/zircote/swagger-php/tree/master/src/Spec/Header.php)

Describes a single HTTP header.

#### Allowed in
---
<a href="#components">Components</a>, <a href="#response">Response</a>

#### Nested elements
---
<a href="#mediatype">MediaType</a>, <a href="#example">Example</a>

#### Parameters
---
- **header** : `string|null`  
  The header name (component key)
- **description** : `string|null`  
  A brief description of the header (CommonMark syntax)
- **required** : `bool|null`  
  Whether the header is mandatory
- **deprecated** : `bool|null`  
  Whether the header is deprecated
- **ref** : `string|null`  
  A JSON Reference to a reusable header
- **style** : `string|null`  
  How the header value is serialized
- **explode** : `bool|null`  
  Whether arrays/objects generate separate parameters
- **schema** : `Schema|null`  
  The schema defining the type for the header
- **example** : `mixed`  
  Example of the header's value
- **examples** : `list&lt;Example&gt;|null`  
  Examples of the header's value
- **content** : `list&lt;MediaType&gt;|null`  
  Content-type based header serialization

#### Reference
---
- [Header Object](https://spec.openapis.org/oas/v3.1.1.html#header-object) ↗

### [Info](https://github.com/zircote/swagger-php/tree/master/src/Spec/Info.php)

Metadata about the API.

#### Nested elements
---
<a href="#contact">Contact</a>, <a href="#license">License</a>

#### Parameters
---
- **title** : `string|null`  
  The title of the API
- **description** : `string|null`  
  A description of the API (CommonMark syntax)
- **termsOfService** : `string|null`  
  A URL to the Terms of Service for the API
- **version** : `string|null`  
  The version of the API document
- **contact** : `Contact|null`  
  Contact information for the API
- **license** : `License|null`  
  License information for the API
- **summary** : `string|null`  
  A short summary of the API

#### Reference
---
- [Info Object](https://spec.openapis.org/oas/v3.1.1.html#info-object) ↗

### [License](https://github.com/zircote/swagger-php/tree/master/src/Spec/License.php)

License information for the exposed API.

#### Allowed in
---
<a href="#info">Info</a>

#### Parameters
---
- **name** : `string|null`  
  The license name used for the API
- **identifier** : `string|null`  
  An SPDX license expression for the API
- **url** : `string|null`  
  A URL to the license used for the API

#### Reference
---
- [License Object](https://spec.openapis.org/oas/v3.1.1.html#license-object) ↗

### [Link](https://github.com/zircote/swagger-php/tree/master/src/Spec/Link.php)

Describes a possible design-time link for a response.

#### Allowed in
---
<a href="#components">Components</a>, <a href="#response">Response</a>

#### Parameters
---
- **link** : `string|null`  
  Reusable link identifier (component key)
- **operationRef** : `string|null`  
  A relative or absolute URI reference to a linked operation
- **operationId** : `string|null`  
  The name of an existing operation (mutually exclusive with operationRef)
- **parameters** : `array&lt;string,mixed&gt;|null`  
  Values to pass to the linked operation's parameters
- **requestBody** : `mixed`  
  A value to use as the request body for the linked operation
- **description** : `string|null`  
  A description of the link (CommonMark syntax)
- **ref** : `string|null`  
  A JSON Reference to a reusable link
- **server** : `Server|null`  
  A server object to be used by the target operation

#### Reference
---
- [Link Object](https://spec.openapis.org/oas/v3.1.1.html#link-object) ↗

### [MediaType](https://github.com/zircote/swagger-php/tree/master/src/Spec/MediaType.php)

Describes the content payload for a specific media type.

#### Allowed in
---
<a href="#header">Header</a>, <a href="#parameter">Parameter</a>, <a href="#parameter-cookie">Parameter\Cookie</a>, <a href="#parameter-header">Parameter\Header</a>, <a href="#parameter-path">Parameter\Path</a>, <a href="#parameter-query">Parameter\Query</a>, <a href="#requestbody">RequestBody</a>, <a href="#response">Response</a>

#### Nested elements
---
<a href="#encoding">Encoding</a>, <a href="#example">Example</a>

#### Parameters
---
- **mediaType** : `string|null`  
  The media type identifier (e.g. 'application/json')
- **schema** : `Schema|null`  
  The schema defining the content
- **example** : `mixed`  
  Example of the media type content
- **examples** : `list&lt;Example&gt;|null`  
  Examples of the media type content
- **encoding** : `list&lt;Encoding&gt;|array&lt;string,Encoding&gt;|null`  
  Encoding information for specific properties

#### Reference
---
- [Media Type Object](https://spec.openapis.org/oas/v3.1.1.html#media-type-object) ↗

### [OpenApi](https://github.com/zircote/swagger-php/tree/master/src/Spec/OpenApi.php)

The root element of an OpenAPI definition.

#### Nested elements
---
<a href="#security-requirement">Security\Requirement</a>

#### Parameters
---
- **version** : `string|null`  
  The OpenAPI specification version (e.g. '3.1.0')
- **security** : `list&lt;Security\Requirement&gt;|null`  
  Default security requirements for the API

#### Reference
---
- [OpenAPI Object](https://spec.openapis.org/oas/v3.1.1.html#openapi-object) ↗

### [Operation](https://github.com/zircote/swagger-php/tree/master/src/Spec/Operation.php)

Describes a single API operation on a path.

Typed subclasses pre-fill the HTTP method — use them instead of specifying method manually:

  #[OA\Operation\Get(path: '/pets/{id}', responses: [
      new OA\Response(response: 200, description: 'A pet', content: [
          new OA\MediaType(schema: new OA\Schema(ref: Pet::class)),
      ]),
  ])]
  public function show(int $id) {}

Produces:
  paths:
    /pets/{id}:
      get:
        operationId: show
        responses:
          '200':
            description: A pet
            content:
              application/json:
                schema:
                  $ref: '#/components/schemas/Pet'

For webhooks, use `webhook` instead of `path`:

  #[OA\Operation\Post(webhook: 'petAdopted', responses: [...])]

#### Nested elements
---
<a href="#parameter">Parameter</a>, <a href="#response">Response</a>, <a href="#requestbody">RequestBody</a>, <a href="#server">Server</a>, <a href="#security-requirement">Security\Requirement</a>

#### Parameters
---
- **path** : `string|null`  
  The URL path for the operation
- **webhook** : `string|null`  
  The webhook name (mutually exclusive with path)
- **method** : `string|null`  
  The HTTP method (get, post, put, delete, etc.)
- **operationId** : `string|null`  
  Unique identifier for the operation
- **summary** : `string|null`  
  A short summary of what the operation does
- **description** : `string|null`  
  A verbose explanation of the operation (CommonMark syntax)
- **tags** : `list&lt;string&gt;|null`  
  Tags for API documentation grouping
- **parameters** : `list&lt;Parameter&gt;|null`  
  Parameters applicable to this operation
- **requestBody** : `RequestBody|null`  
  The request body applicable to this operation
- **responses** : `list&lt;Response&gt;|null`  
  The list of possible responses
- **callbacks** : `array&lt;string,mixed&gt;|null`  
  Possible out-of-band callbacks related to the operation
- **deprecated** : `bool|null`  
  Whether the operation is deprecated
- **security** : `list&lt;Security\Requirement&gt;|null`  
  Security mechanisms that can be used for this operation
- **servers** : `list&lt;Server&gt;|null`  
  Alternative servers for this operation
- **externalDocs** : `ExternalDocumentation|null`  
  Additional external documentation

#### Reference
---
- [Operation Object](https://spec.openapis.org/oas/v3.1.1.html#operation-object) ↗
- [Webhooks](https://spec.openapis.org/oas/v3.1.1.html#fixed-fields) ↗

### [Operation\Delete](https://github.com/zircote/swagger-php/tree/master/src/Spec/Operation/Delete.php)

Shorthand for an HTTP DELETE operation.

#### Nested elements
---
<a href="#parameter">Parameter</a>, <a href="#response">Response</a>, <a href="#requestbody">RequestBody</a>, <a href="#server">Server</a>, <a href="#security-requirement">Security\Requirement</a>

#### Parameters
---
- **path** : `string|null`  
  No details available.
- **webhook** : `string|null`  
  No details available.
- **operationId** : `string|null`  
  No details available.
- **summary** : `string|null`  
  No details available.
- **description** : `string|null`  
  No details available.
- **tags** : `list&lt;string&gt;|null`  
  No details available.
- **parameters** : `list&lt;OA\Parameter&gt;|null`  
  No details available.
- **requestBody** : `OpenApi\Spec\RequestBody|null`  
  No details available.
- **responses** : `list&lt;OA\Response&gt;|null`  
  No details available.
- **callbacks** : `array&lt;string,mixed&gt;|null`  
  No details available.
- **deprecated** : `bool|null`  
  No details available.
- **security** : `list&lt;OA\Security\Requirement&gt;|null`  
  No details available.
- **servers** : `list&lt;OA\Server&gt;|null`  
  No details available.
- **externalDocs** : `OpenApi\Spec\ExternalDocumentation|null`  
  No details available.

#### Reference
---
- [Operation Object](https://spec.openapis.org/oas/v3.1.1.html#operation-object) ↗

### [Operation\Get](https://github.com/zircote/swagger-php/tree/master/src/Spec/Operation/Get.php)

Shorthand for an HTTP GET operation.

#### Nested elements
---
<a href="#parameter">Parameter</a>, <a href="#response">Response</a>, <a href="#requestbody">RequestBody</a>, <a href="#server">Server</a>, <a href="#security-requirement">Security\Requirement</a>

#### Parameters
---
- **path** : `string|null`  
  No details available.
- **webhook** : `string|null`  
  No details available.
- **operationId** : `string|null`  
  No details available.
- **summary** : `string|null`  
  No details available.
- **description** : `string|null`  
  No details available.
- **tags** : `list&lt;string&gt;|null`  
  No details available.
- **parameters** : `list&lt;OA\Parameter&gt;|null`  
  No details available.
- **requestBody** : `OpenApi\Spec\RequestBody|null`  
  No details available.
- **responses** : `list&lt;OA\Response&gt;|null`  
  No details available.
- **callbacks** : `array&lt;string,mixed&gt;|null`  
  No details available.
- **deprecated** : `bool|null`  
  No details available.
- **security** : `list&lt;OA\Security\Requirement&gt;|null`  
  No details available.
- **servers** : `list&lt;OA\Server&gt;|null`  
  No details available.
- **externalDocs** : `OpenApi\Spec\ExternalDocumentation|null`  
  No details available.

#### Reference
---
- [Operation Object](https://spec.openapis.org/oas/v3.1.1.html#operation-object) ↗

### [Operation\Head](https://github.com/zircote/swagger-php/tree/master/src/Spec/Operation/Head.php)

Shorthand for an HTTP HEAD operation.

#### Nested elements
---
<a href="#parameter">Parameter</a>, <a href="#response">Response</a>, <a href="#requestbody">RequestBody</a>, <a href="#server">Server</a>, <a href="#security-requirement">Security\Requirement</a>

#### Parameters
---
- **path** : `string|null`  
  No details available.
- **webhook** : `string|null`  
  No details available.
- **operationId** : `string|null`  
  No details available.
- **summary** : `string|null`  
  No details available.
- **description** : `string|null`  
  No details available.
- **tags** : `list&lt;string&gt;|null`  
  No details available.
- **parameters** : `list&lt;OA\Parameter&gt;|null`  
  No details available.
- **requestBody** : `OpenApi\Spec\RequestBody|null`  
  No details available.
- **responses** : `list&lt;OA\Response&gt;|null`  
  No details available.
- **callbacks** : `array&lt;string,mixed&gt;|null`  
  No details available.
- **deprecated** : `bool|null`  
  No details available.
- **security** : `list&lt;OA\Security\Requirement&gt;|null`  
  No details available.
- **servers** : `list&lt;OA\Server&gt;|null`  
  No details available.
- **externalDocs** : `OpenApi\Spec\ExternalDocumentation|null`  
  No details available.

#### Reference
---
- [Operation Object](https://spec.openapis.org/oas/v3.1.1.html#operation-object) ↗

### [Operation\Options](https://github.com/zircote/swagger-php/tree/master/src/Spec/Operation/Options.php)

Shorthand for an HTTP OPTIONS operation.

#### Nested elements
---
<a href="#parameter">Parameter</a>, <a href="#response">Response</a>, <a href="#requestbody">RequestBody</a>, <a href="#server">Server</a>, <a href="#security-requirement">Security\Requirement</a>

#### Parameters
---
- **path** : `string|null`  
  No details available.
- **webhook** : `string|null`  
  No details available.
- **operationId** : `string|null`  
  No details available.
- **summary** : `string|null`  
  No details available.
- **description** : `string|null`  
  No details available.
- **tags** : `list&lt;string&gt;|null`  
  No details available.
- **parameters** : `list&lt;OA\Parameter&gt;|null`  
  No details available.
- **requestBody** : `OpenApi\Spec\RequestBody|null`  
  No details available.
- **responses** : `list&lt;OA\Response&gt;|null`  
  No details available.
- **callbacks** : `array&lt;string,mixed&gt;|null`  
  No details available.
- **deprecated** : `bool|null`  
  No details available.
- **security** : `list&lt;OA\Security\Requirement&gt;|null`  
  No details available.
- **servers** : `list&lt;OA\Server&gt;|null`  
  No details available.
- **externalDocs** : `OpenApi\Spec\ExternalDocumentation|null`  
  No details available.

#### Reference
---
- [Operation Object](https://spec.openapis.org/oas/v3.1.1.html#operation-object) ↗

### [Operation\Patch](https://github.com/zircote/swagger-php/tree/master/src/Spec/Operation/Patch.php)

Shorthand for an HTTP PATCH operation.

#### Nested elements
---
<a href="#parameter">Parameter</a>, <a href="#response">Response</a>, <a href="#requestbody">RequestBody</a>, <a href="#server">Server</a>, <a href="#security-requirement">Security\Requirement</a>

#### Parameters
---
- **path** : `string|null`  
  No details available.
- **webhook** : `string|null`  
  No details available.
- **operationId** : `string|null`  
  No details available.
- **summary** : `string|null`  
  No details available.
- **description** : `string|null`  
  No details available.
- **tags** : `list&lt;string&gt;|null`  
  No details available.
- **parameters** : `list&lt;OA\Parameter&gt;|null`  
  No details available.
- **requestBody** : `OpenApi\Spec\RequestBody|null`  
  No details available.
- **responses** : `list&lt;OA\Response&gt;|null`  
  No details available.
- **callbacks** : `array&lt;string,mixed&gt;|null`  
  No details available.
- **deprecated** : `bool|null`  
  No details available.
- **security** : `list&lt;OA\Security\Requirement&gt;|null`  
  No details available.
- **servers** : `list&lt;OA\Server&gt;|null`  
  No details available.
- **externalDocs** : `OpenApi\Spec\ExternalDocumentation|null`  
  No details available.

#### Reference
---
- [Operation Object](https://spec.openapis.org/oas/v3.1.1.html#operation-object) ↗

### [Operation\Post](https://github.com/zircote/swagger-php/tree/master/src/Spec/Operation/Post.php)

Shorthand for an HTTP POST operation.

#### Nested elements
---
<a href="#parameter">Parameter</a>, <a href="#response">Response</a>, <a href="#requestbody">RequestBody</a>, <a href="#server">Server</a>, <a href="#security-requirement">Security\Requirement</a>

#### Parameters
---
- **path** : `string|null`  
  No details available.
- **webhook** : `string|null`  
  No details available.
- **operationId** : `string|null`  
  No details available.
- **summary** : `string|null`  
  No details available.
- **description** : `string|null`  
  No details available.
- **tags** : `list&lt;string&gt;|null`  
  No details available.
- **parameters** : `list&lt;OA\Parameter&gt;|null`  
  No details available.
- **requestBody** : `OpenApi\Spec\RequestBody|null`  
  No details available.
- **responses** : `list&lt;OA\Response&gt;|null`  
  No details available.
- **callbacks** : `array&lt;string,mixed&gt;|null`  
  No details available.
- **deprecated** : `bool|null`  
  No details available.
- **security** : `list&lt;OA\Security\Requirement&gt;|null`  
  No details available.
- **servers** : `list&lt;OA\Server&gt;|null`  
  No details available.
- **externalDocs** : `OpenApi\Spec\ExternalDocumentation|null`  
  No details available.

#### Reference
---
- [Operation Object](https://spec.openapis.org/oas/v3.1.1.html#operation-object) ↗

### [Operation\Put](https://github.com/zircote/swagger-php/tree/master/src/Spec/Operation/Put.php)

Shorthand for an HTTP PUT operation.

#### Nested elements
---
<a href="#parameter">Parameter</a>, <a href="#response">Response</a>, <a href="#requestbody">RequestBody</a>, <a href="#server">Server</a>, <a href="#security-requirement">Security\Requirement</a>

#### Parameters
---
- **path** : `string|null`  
  No details available.
- **webhook** : `string|null`  
  No details available.
- **operationId** : `string|null`  
  No details available.
- **summary** : `string|null`  
  No details available.
- **description** : `string|null`  
  No details available.
- **tags** : `list&lt;string&gt;|null`  
  No details available.
- **parameters** : `list&lt;OA\Parameter&gt;|null`  
  No details available.
- **requestBody** : `OpenApi\Spec\RequestBody|null`  
  No details available.
- **responses** : `list&lt;OA\Response&gt;|null`  
  No details available.
- **callbacks** : `array&lt;string,mixed&gt;|null`  
  No details available.
- **deprecated** : `bool|null`  
  No details available.
- **security** : `list&lt;OA\Security\Requirement&gt;|null`  
  No details available.
- **servers** : `list&lt;OA\Server&gt;|null`  
  No details available.
- **externalDocs** : `OpenApi\Spec\ExternalDocumentation|null`  
  No details available.

#### Reference
---
- [Operation Object](https://spec.openapis.org/oas/v3.1.1.html#operation-object) ↗

### [Operation\Trace](https://github.com/zircote/swagger-php/tree/master/src/Spec/Operation/Trace.php)

Shorthand for an HTTP TRACE operation.

#### Nested elements
---
<a href="#parameter">Parameter</a>, <a href="#response">Response</a>, <a href="#requestbody">RequestBody</a>, <a href="#server">Server</a>, <a href="#security-requirement">Security\Requirement</a>

#### Parameters
---
- **path** : `string|null`  
  No details available.
- **webhook** : `string|null`  
  No details available.
- **operationId** : `string|null`  
  No details available.
- **summary** : `string|null`  
  No details available.
- **description** : `string|null`  
  No details available.
- **tags** : `list&lt;string&gt;|null`  
  No details available.
- **parameters** : `list&lt;OA\Parameter&gt;|null`  
  No details available.
- **requestBody** : `OpenApi\Spec\RequestBody|null`  
  No details available.
- **responses** : `list&lt;OA\Response&gt;|null`  
  No details available.
- **callbacks** : `array&lt;string,mixed&gt;|null`  
  No details available.
- **deprecated** : `bool|null`  
  No details available.
- **security** : `list&lt;OA\Security\Requirement&gt;|null`  
  No details available.
- **servers** : `list&lt;OA\Server&gt;|null`  
  No details available.
- **externalDocs** : `OpenApi\Spec\ExternalDocumentation|null`  
  No details available.

#### Reference
---
- [Operation Object](https://spec.openapis.org/oas/v3.1.1.html#operation-object) ↗

### [Parameter](https://github.com/zircote/swagger-php/tree/master/src/Spec/Parameter.php)

Describes a single operation parameter.

Typed subtypes pre-fill `in` (and `required` for path):
- `OA\Parameter\Path` - path parameters (in: path, required: true)
- `OA\Parameter\Query` - query string parameters (in: query)
- `OA\Parameter\Header` - header parameters (in: header)
- `OA\Parameter\Cookie` - cookie parameters (in: cookie)

Inline on an operation:

  #[OA\Operation\Get(path: '/pets', parameters: [
      new OA\Parameter\Query(name: 'status', schema: new OA\Schema(type: 'string', enum: ['active', 'sold'])),
  ])]

Or as a reusable component (set `parameter` for the component key):

  #[OA\Parameter\Path(parameter: 'petId', name: 'id', schema: new OA\Schema(type: 'integer'))]

Produces:
  components:
    parameters:
      petId:
        name: id
        in: path
        required: true
        schema:
          type: integer

#### Allowed in
---
<a href="#components">Components</a>, <a href="#operation">Operation</a>, <a href="#operation-delete">Operation\Delete</a>, <a href="#operation-get">Operation\Get</a>, <a href="#operation-head">Operation\Head</a>, <a href="#operation-options">Operation\Options</a>, <a href="#operation-patch">Operation\Patch</a>, <a href="#operation-post">Operation\Post</a>, <a href="#operation-put">Operation\Put</a>, <a href="#operation-trace">Operation\Trace</a>, <a href="#pathitem">PathItem</a>

#### Nested elements
---
<a href="#mediatype">MediaType</a>, <a href="#example">Example</a>

#### Parameters
---
- **parameter** : `string|null`  
  Reusable parameter identifier (component key)
- **name** : `string|null`  
  The name of the parameter
- **in** : `string|null`  
  The location of the parameter (query, header, path, cookie)
- **description** : `string|null`  
  A brief description of the parameter (CommonMark syntax)
- **required** : `bool|null`  
  Whether the parameter is mandatory
- **deprecated** : `bool|null`  
  Whether the parameter is deprecated
- **allowEmptyValue** : `bool|null`  
  Whether empty-valued parameters are allowed
- **ref** : `string|null`  
  A JSON Reference to a reusable parameter
- **style** : `string|null`  
  How the parameter value is serialized
- **explode** : `bool|null`  
  Whether arrays/objects generate separate parameters
- **allowReserved** : `bool|null`  
  Whether reserved characters are allowed without encoding
- **schema** : `Schema|null`  
  The schema defining the type for the parameter
- **example** : `mixed`  
  Example of the parameter's value
- **examples** : `list&lt;Example&gt;|null`  
  Examples of the parameter's value
- **content** : `list&lt;MediaType&gt;|null`  
  Content-type based parameter serialization

#### Reference
---
- [Parameter Object](https://spec.openapis.org/oas/v3.1.1.html#parameter-object) ↗

### [Parameter\Cookie](https://github.com/zircote/swagger-php/tree/master/src/Spec/Parameter/Cookie.php)

A parameter passed via an HTTP cookie.

#### Allowed in
---
<a href="#operation">Operation</a>, <a href="#pathitem">PathItem</a>

#### Nested elements
---
<a href="#mediatype">MediaType</a>, <a href="#example">Example</a>

#### Parameters
---
- **parameter** : `string|null`  
  No details available.
- **name** : `string|null`  
  No details available.
- **description** : `string|null`  
  No details available.
- **required** : `bool|null`  
  No details available.
- **deprecated** : `bool|null`  
  No details available.
- **ref** : `string|null`  
  No details available.
- **explode** : `bool|null`  
  No details available.
- **schema** : `OpenApi\Spec\Schema|null`  
  No details available.
- **example** : `mixed|null`  
  No details available.
- **examples** : `list&lt;OA\Example&gt;|null`  
  No details available.
- **content** : `list&lt;OA\MediaType&gt;|null`  
  No details available.

#### Reference
---
- [Parameter Object](https://spec.openapis.org/oas/v3.1.1.html#parameter-object) ↗

### [Parameter\Header](https://github.com/zircote/swagger-php/tree/master/src/Spec/Parameter/Header.php)

A parameter passed via an HTTP header.

#### Allowed in
---
<a href="#operation">Operation</a>, <a href="#pathitem">PathItem</a>

#### Nested elements
---
<a href="#mediatype">MediaType</a>, <a href="#example">Example</a>

#### Parameters
---
- **parameter** : `string|null`  
  No details available.
- **name** : `string|null`  
  No details available.
- **description** : `string|null`  
  No details available.
- **required** : `bool|null`  
  No details available.
- **deprecated** : `bool|null`  
  No details available.
- **ref** : `string|null`  
  No details available.
- **explode** : `bool|null`  
  No details available.
- **schema** : `OpenApi\Spec\Schema|null`  
  No details available.
- **example** : `mixed|null`  
  No details available.
- **examples** : `list&lt;OA\Example&gt;|null`  
  No details available.
- **content** : `list&lt;OA\MediaType&gt;|null`  
  No details available.

#### Reference
---
- [Parameter Object](https://spec.openapis.org/oas/v3.1.1.html#parameter-object) ↗

### [Parameter\Path](https://github.com/zircote/swagger-php/tree/master/src/Spec/Parameter/Path.php)

A parameter passed via the URL path (always required).

#### Allowed in
---
<a href="#operation">Operation</a>, <a href="#pathitem">PathItem</a>

#### Nested elements
---
<a href="#mediatype">MediaType</a>, <a href="#example">Example</a>

#### Parameters
---
- **parameter** : `string|null`  
  No details available.
- **name** : `string|null`  
  No details available.
- **description** : `string|null`  
  No details available.
- **deprecated** : `bool|null`  
  No details available.
- **ref** : `string|null`  
  No details available.
- **style** : `string|null`  
  No details available.
- **explode** : `bool|null`  
  No details available.
- **schema** : `OpenApi\Spec\Schema|null`  
  No details available.
- **example** : `mixed|null`  
  No details available.
- **examples** : `list&lt;OA\Example&gt;|null`  
  No details available.
- **content** : `list&lt;OA\MediaType&gt;|null`  
  No details available.

#### Reference
---
- [Parameter Object](https://spec.openapis.org/oas/v3.1.1.html#parameter-object) ↗

### [Parameter\Query](https://github.com/zircote/swagger-php/tree/master/src/Spec/Parameter/Query.php)

A parameter passed via the URL query string.

#### Allowed in
---
<a href="#operation">Operation</a>, <a href="#pathitem">PathItem</a>

#### Nested elements
---
<a href="#mediatype">MediaType</a>, <a href="#example">Example</a>

#### Parameters
---
- **parameter** : `string|null`  
  No details available.
- **name** : `string|null`  
  No details available.
- **description** : `string|null`  
  No details available.
- **required** : `bool|null`  
  No details available.
- **deprecated** : `bool|null`  
  No details available.
- **allowEmptyValue** : `bool|null`  
  No details available.
- **ref** : `string|null`  
  No details available.
- **style** : `string|null`  
  No details available.
- **explode** : `bool|null`  
  No details available.
- **allowReserved** : `bool|null`  
  No details available.
- **schema** : `OpenApi\Spec\Schema|null`  
  No details available.
- **example** : `mixed|null`  
  No details available.
- **examples** : `list&lt;OA\Example&gt;|null`  
  No details available.
- **content** : `list&lt;OA\MediaType&gt;|null`  
  No details available.

#### Reference
---
- [Parameter Object](https://spec.openapis.org/oas/v3.1.1.html#parameter-object) ↗

### [PathItem](https://github.com/zircote/swagger-php/tree/master/src/Spec/PathItem.php)

Describes shared metadata for all operations under a path.

Place on a controller class — the path is inferred from its operations.
Parameters, summary, description and servers are emitted at path level in the
OpenAPI output. Prefix, tags, security and responses are controller-level features
that compose via class hierarchy and apply to all contained operations.

Shared path-level properties (parameters, summary, description, servers per OpenAPI spec):

  #[PathItem(parameters: [new Parameter\Path(name: 'id', schema: new Schema(type: 'integer'))])]
  class ProductController {
      #[Operation\Get(path: '/products/{id}')]
      public function get() {}
  }

The path in the output is inferred from the operations — no need to declare it
on PathItem, avoiding duplication.

Prefix composition with inherited metadata:

  #[PathItem(prefix: '/api/v1')]
  class BaseController {}

  #[PathItem(prefix: '/users', tags: ['Users'], security: [new Security\Requirement(scheme: 'bearerAuth')])]
  #[Response(response: 401, description: 'Unauthorized')]
  #[Response(response: 500, description: 'Server error')]
  class UserController extends BaseController {
      #[Operation\Get(path: '/list')]       // resolved: /api/v1/users/list, tags: ['Users']
      public function list() {}

      #[Operation\Get(path: '/{id}')]       // resolved: /api/v1/users/{id}, tags: ['Users']
      public function get() {}
  }

Prefixes compose by walking the class hierarchy — each ancestor PathItem contributes
its prefix segment. All collection properties merge additively: tags, security,
responses, and parameters accumulate from the full ancestor chain. Deduplication
is by value (tags), by scheme (security), by status code (responses), and by
name+in (parameters).

#### Allowed in
---
<a href="#components">Components</a>

#### Nested elements
---
<a href="#parameter">Parameter</a>, <a href="#server">Server</a>, <a href="#response">Response</a>, <a href="#security-requirement">Security\Requirement</a>

#### Parameters
---
- **ref** : `string|null`  
  A JSON Reference to a reusable path item
- **prefix** : `string|null`  
  Path prefix — composable via class hierarchy
- **summary** : `string|null`  
  An optional summary, intended to apply to all operations in this path
- **description** : `string|null`  
  An optional description, intended to apply to all operations in this path
- **parameters** : `list&lt;Parameter&gt;|null`  
  Parameters applicable to all operations under this path
- **servers** : `list&lt;Server&gt;|null`  
  Alternative servers for all operations under this path
- **tags** : `list&lt;string&gt;|null`  
  Tags to clone to contained operations
- **security** : `list&lt;Security\Requirement&gt;|null`  
  Security requirements to clone to contained operations
- **responses** : `list&lt;Response&gt;|null`  
  Shared responses to clone to contained operations

#### Reference
---
- [Path Item Object](https://spec.openapis.org/oas/v3.1.1.html#path-item-object) ↗

### [Property](https://github.com/zircote/swagger-php/tree/master/src/Spec/Property.php)

Defines a single property within a Schema object.

#### Allowed in
---
<a href="#schema">Schema</a>

#### Parameters
---
- **property** : `string|null`  
  The property name
- **schema** : `Schema|null`  
  The schema defining the property type and constraints

#### Reference
---
- [Schema Object](https://spec.openapis.org/oas/v3.1.1.html#schema-object) ↗

### [RequestBody](https://github.com/zircote/swagger-php/tree/master/src/Spec/RequestBody.php)

Describes a single request body.

#### Allowed in
---
<a href="#components">Components</a>, <a href="#operation">Operation</a>, <a href="#operation-delete">Operation\Delete</a>, <a href="#operation-get">Operation\Get</a>, <a href="#operation-head">Operation\Head</a>, <a href="#operation-options">Operation\Options</a>, <a href="#operation-patch">Operation\Patch</a>, <a href="#operation-post">Operation\Post</a>, <a href="#operation-put">Operation\Put</a>, <a href="#operation-trace">Operation\Trace</a>

#### Nested elements
---
<a href="#mediatype">MediaType</a>

#### Parameters
---
- **request** : `string|null`  
  Reusable request body identifier (component key)
- **description** : `string|null`  
  A brief description of the request body (CommonMark syntax)
- **required** : `bool|null`  
  Whether the request body is required
- **ref** : `string|null`  
  A JSON Reference to a reusable request body
- **content** : `list&lt;MediaType&gt;|null`  
  The content of the request body

#### Reference
---
- [Request Body Object](https://spec.openapis.org/oas/v3.1.1.html#request-body-object) ↗

### [Response](https://github.com/zircote/swagger-php/tree/master/src/Spec/Response.php)

Describes a single response from an API operation.

#### Allowed in
---
<a href="#components">Components</a>, <a href="#operation">Operation</a>, <a href="#operation-delete">Operation\Delete</a>, <a href="#operation-get">Operation\Get</a>, <a href="#operation-head">Operation\Head</a>, <a href="#operation-options">Operation\Options</a>, <a href="#operation-patch">Operation\Patch</a>, <a href="#operation-post">Operation\Post</a>, <a href="#operation-put">Operation\Put</a>, <a href="#operation-trace">Operation\Trace</a>, <a href="#pathitem">PathItem</a>

#### Nested elements
---
<a href="#header">Header</a>, <a href="#mediatype">MediaType</a>, <a href="#link">Link</a>

#### Parameters
---
- **response** : `string|int|null`  
  The HTTP status code or 'default'
- **description** : `string|null`  
  A description of the response (CommonMark syntax)
- **ref** : `string|null`  
  A JSON Reference to a reusable response
- **headers** : `list&lt;Header&gt;|null`  
  Headers sent with the response
- **content** : `list&lt;MediaType&gt;|null`  
  Possible response payloads
- **links** : `list&lt;Link&gt;|null`  
  Design-time links for the response

#### Reference
---
- [Response Object](https://spec.openapis.org/oas/v3.1.1.html#response-object) ↗

### [Schema](https://github.com/zircote/swagger-php/tree/master/src/Spec/Schema.php)

Defines the structure and validation rules for a data type.

On a class — becomes a reusable component schema (name inferred from class):

  #[OA\Schema]
  class Pet {
      #[OA\Property]
      public string $name;
      #[OA\Property]
      public ?int $age;
  }

Produces:
  components:
    schemas:
      Pet:
        type: object
        properties:
          name: { type: string }
          age: { type: integer, nullable: true }

Inline — used within parameters, responses, or other schemas:

  new OA\Schema(type: 'array', items: new OA\Schema(ref: Pet::class))

#### Allowed in
---
<a href="#components">Components</a>, <a href="#schema">Schema</a>

#### Nested elements
---
<a href="#property">Property</a>, <a href="#schema">Schema</a>

#### Parameters
---
- **schema** : `string|null`  
  Reusable schema identifier (component key)
- **title** : `string|null`  
  A title for the schema
- **description** : `string|null`  
  A description of the schema (CommonMark syntax)
- **ref** : `string|null`  
  A JSON Reference to a reusable schema
- **type** : `string|list&lt;string&gt;|null`  
  The value type(s) (string, number, integer, boolean, array, object, null)
- **format** : `string|null`  
  Further refines the type (e.g. int32, int64, float, double, date-time, email)
- **nullable** : `bool|null`  
  Whether the value can be null (OAS 3.0 only; use type array in 3.1+)
- **minLength** : `int|null`  
  Minimum string length
- **maxLength** : `int|null`  
  Maximum string length
- **pattern** : `string|null`  
  Regular expression pattern the string must match
- **contentMediaType** : `string|null`  
  The media type of string content encoding
- **contentEncoding** : `string|null`  
  The encoding used for string content (e.g. base64)
- **minimum** : `int|float|null`  
  Minimum numeric value (inclusive)
- **maximum** : `int|float|null`  
  Maximum numeric value (inclusive)
- **exclusiveMinimum** : `int|float|bool|null`  
  Exclusive minimum value
- **exclusiveMaximum** : `int|float|bool|null`  
  Exclusive maximum value
- **multipleOf** : `int|float|null`  
  The value must be a multiple of this number
- **items** : `Schema|string|null`  
  Schema for array items
- **minItems** : `int|null`  
  Minimum number of array items
- **maxItems** : `int|null`  
  Maximum number of array items
- **uniqueItems** : `bool|null`  
  Whether array items must be unique
- **prefixItems** : `list&lt;Schema&gt;|null`  
  Schemas for positional array items (tuple validation)
- **contains** : `Schema|bool|null`  
  Schema that at least one array item must match
- **minContains** : `int|null`  
  Minimum number of items matching contains
- **maxContains** : `int|null`  
  Maximum number of items matching contains
- **unevaluatedItems** : `Schema|bool|null`  
  Schema for items not covered by other keywords
- **properties** : `list&lt;Property|Schema&gt;|null`  
  Object property definitions
- **required** : `list&lt;string&gt;|null`  
  List of required property names
- **additionalProperties** : `Schema|bool|null`  
  Schema or boolean for additional properties
- **patternProperties** : `array&lt;string,Schema&gt;|null`  
  Schemas for properties matching regex patterns
- **minProperties** : `int|null`  
  Minimum number of properties
- **maxProperties** : `int|null`  
  Maximum number of properties
- **unevaluatedProperties** : `Schema|bool|null`  
  Schema for properties not covered by other keywords
- **propertyNames** : `Schema|null`  
  Schema that property names must validate against
- **dependentRequired** : `array&lt;string,list&lt;string&gt;&gt;|null`  
  Property-level required dependencies
- **dependentSchemas** : `array&lt;string,Schema&gt;|null`  
  Property-level schema dependencies
- **allOf** : `list&lt;Schema&gt;|null`  
  All schemas must match (AND composition)
- **anyOf** : `list&lt;Schema&gt;|null`  
  At least one schema must match (OR composition)
- **oneOf** : `list&lt;Schema&gt;|null`  
  Exactly one schema must match (XOR composition)
- **not** : `Schema|null`  
  The schema must NOT match
- **if** : `Schema|null`  
  Conditional schema (if-then-else)
- **then** : `Schema|null`  
  Applied when 'if' succeeds
- **else** : `Schema|null`  
  Applied when 'if' fails
- **enum** : `list&lt;string|int|float|bool|\UnitEnum|class-string&lt;\UnitEnum&gt;|null&gt;|null`  
  Allowed values
- **const** : `mixed`  
  A single allowed value
- **example** : `mixed`  
  An example value
- **examples** : `list&lt;mixed&gt;|null`  
  A list of example values
- **deprecated** : `bool|null`  
  Whether the schema is deprecated
- **readOnly** : `bool|null`  
  Whether the value is read-only
- **writeOnly** : `bool|null`  
  Whether the value is write-only
- **default** : `mixed`  
  The default value
- **discriminator** : `Discriminator|null`  
  Discriminator for polymorphism
- **externalDocs** : `ExternalDocumentation|null`  
  Additional external documentation
- **xml** : `Xml|null`  
  XML representation metadata

#### Reference
---
- [Schema Object](https://spec.openapis.org/oas/v3.1.1.html#schema-object) ↗
- [JSON Schema](https://json-schema.org/draft/2020-12/json-schema-validation) ↗

### [Security\Requirement](https://github.com/zircote/swagger-php/tree/master/src/Spec/Security/Requirement.php)

A security requirement declaring which security schemes apply.

Each requirement instance represents one entry in the security array (OR logic).
Multiple schemes within a single requirement represent AND logic.

#### Allowed in
---
<a href="#openapi">OpenApi</a>, <a href="#operation">Operation</a>, <a href="#operation-delete">Operation\Delete</a>, <a href="#operation-get">Operation\Get</a>, <a href="#operation-head">Operation\Head</a>, <a href="#operation-options">Operation\Options</a>, <a href="#operation-patch">Operation\Patch</a>, <a href="#operation-post">Operation\Post</a>, <a href="#operation-put">Operation\Put</a>, <a href="#operation-trace">Operation\Trace</a>, <a href="#pathitem">PathItem</a>

#### Parameters
---
- **scheme** : `string|null`  
  Single scheme name (shorthand for simple requirements)
- **scopes** : `list&lt;string&gt;|null`  
  Scopes for the single scheme (OAuth2/OpenIdConnect)
- **schemes** : `array&lt;string,list&lt;string&gt;&gt;|null`  
  Map of scheme names to scopes (for AND logic with multiple schemes)

#### Reference
---
- [Security Requirement Object](https://spec.openapis.org/oas/v3.1.1.html#security-requirement-object) ↗

### [Security\Scheme](https://github.com/zircote/swagger-php/tree/master/src/Spec/Security/Scheme.php)

Defines a security scheme that can be used by the operations.

Typed subtypes are available for each security scheme type:
- `OA\Security\Scheme\Http` - HTTP authentication (Basic, Bearer, etc.)
- `OA\Security\Scheme\ApiKey` - API key in header, query, or cookie
- `OA\Security\Scheme\OAuth2` - OAuth2 with one or more flows
- `OA\Security\Scheme\OpenIdConnect` - OpenID Connect discovery
- `OA\Security\Scheme\MutualTls` - Mutual TLS authentication

#### Allowed in
---
<a href="#components">Components</a>

#### Nested elements
---
<a href="#flow">Flow</a>

#### Parameters
---
- **securityScheme** : `string|null`  
  Reusable security scheme identifier (component key)
- **type** : `string|null`  
  The type of the security scheme (apiKey, http, mutualTLS, oauth2, openIdConnect)
- **description** : `string|null`  
  A description of the security scheme (CommonMark syntax)
- **name** : `string|null`  
  The name of the header, query, or cookie parameter (apiKey)
- **in** : `string|null`  
  The location of the API key (query, header, cookie)
- **scheme** : `string|null`  
  The HTTP authorization scheme (http)
- **bearerFormat** : `string|null`  
  A hint about the format of the bearer token (http/bearer)
- **openIdConnectUrl** : `string|null`  
  The OpenID Connect URL to discover configuration (openIdConnect)
- **flows** : `list&lt;OA\Flow&gt;|null`  
  The available OAuth2 flows (oauth2)
- **ref** : `string|null`  
  A JSON Reference to a reusable security scheme

#### Reference
---
- [Security Scheme Object](https://spec.openapis.org/oas/v3.1.1.html#security-scheme-object) ↗

### [Security\Scheme\ApiKey](https://github.com/zircote/swagger-php/tree/master/src/Spec/Security/Scheme/ApiKey.php)

An API key security scheme (header, query, or cookie).

#### Nested elements
---
<a href="#flow">Flow</a>

#### Parameters
---
- **securityScheme** : `string|null`  
  No details available.
- **description** : `string|null`  
  No details available.
- **name** : `string|null`  
  No details available.
- **in** : `string|null`  
  No details available.

#### Reference
---
- [Security Scheme Object](https://spec.openapis.org/oas/v3.1.1.html#security-scheme-object) ↗

### [Security\Scheme\Http](https://github.com/zircote/swagger-php/tree/master/src/Spec/Security/Scheme/Http.php)

An HTTP authentication security scheme (Basic, Bearer, etc.).

#### Nested elements
---
<a href="#flow">Flow</a>

#### Parameters
---
- **securityScheme** : `string|null`  
  No details available.
- **description** : `string|null`  
  No details available.
- **scheme** : `string|null`  
  No details available.
- **bearerFormat** : `string|null`  
  No details available.

#### Reference
---
- [Security Scheme Object](https://spec.openapis.org/oas/v3.1.1.html#security-scheme-object) ↗

### [Security\Scheme\MutualTls](https://github.com/zircote/swagger-php/tree/master/src/Spec/Security/Scheme/MutualTls.php)

A Mutual TLS security scheme.

#### Nested elements
---
<a href="#flow">Flow</a>

#### Parameters
---
- **securityScheme** : `string|null`  
  No details available.
- **description** : `string|null`  
  No details available.

#### Reference
---
- [Security Scheme Object](https://spec.openapis.org/oas/v3.1.1.html#security-scheme-object) ↗

### [Security\Scheme\OAuth2](https://github.com/zircote/swagger-php/tree/master/src/Spec/Security/Scheme/OAuth2.php)

An OAuth2 security scheme with one or more flows.

#### Nested elements
---
<a href="#flow">Flow</a>

#### Parameters
---
- **securityScheme** : `string|null`  
  No details available.
- **description** : `string|null`  
  No details available.
- **flows** : `list&lt;OA\Flow&gt;|null`  
  No details available.

#### Reference
---
- [Security Scheme Object](https://spec.openapis.org/oas/v3.1.1.html#security-scheme-object) ↗

### [Security\Scheme\OpenIdConnect](https://github.com/zircote/swagger-php/tree/master/src/Spec/Security/Scheme/OpenIdConnect.php)

An OpenID Connect Discovery security scheme.

#### Nested elements
---
<a href="#flow">Flow</a>

#### Parameters
---
- **securityScheme** : `string|null`  
  No details available.
- **description** : `string|null`  
  No details available.
- **openIdConnectUrl** : `string|null`  
  No details available.

#### Reference
---
- [Security Scheme Object](https://spec.openapis.org/oas/v3.1.1.html#security-scheme-object) ↗

### [Server](https://github.com/zircote/swagger-php/tree/master/src/Spec/Server.php)

Represents a Server.

#### Allowed in
---
<a href="#operation">Operation</a>, <a href="#operation-delete">Operation\Delete</a>, <a href="#operation-get">Operation\Get</a>, <a href="#operation-head">Operation\Head</a>, <a href="#operation-options">Operation\Options</a>, <a href="#operation-patch">Operation\Patch</a>, <a href="#operation-post">Operation\Post</a>, <a href="#operation-put">Operation\Put</a>, <a href="#operation-trace">Operation\Trace</a>, <a href="#pathitem">PathItem</a>

#### Nested elements
---
<a href="#servervariable">ServerVariable</a>

#### Parameters
---
- **url** : `string|null`  
  A URL to the target host
- **description** : `string|null`  
  A description of the host (CommonMark syntax)
- **variables** : `list&lt;ServerVariable&gt;|null`  
  Variables for server URL template substitution

#### Reference
---
- [Server Object](https://spec.openapis.org/oas/v3.1.1.html#server-object) ↗

### [ServerVariable](https://github.com/zircote/swagger-php/tree/master/src/Spec/ServerVariable.php)

Represents a Server Variable for server URL template substitution.

#### Allowed in
---
<a href="#server">Server</a>

#### Parameters
---
- **serverVariable** : `string|null`  
  The variable name
- **default** : `string|null`  
  The default value to use for substitution
- **description** : `string|null`  
  A description of the server variable (CommonMark syntax)
- **enum** : `list&lt;string&gt;|null`  
  Enumeration of allowed string values for substitution

#### Reference
---
- [Server Variable Object](https://spec.openapis.org/oas/v3.1.1.html#server-variable-object) ↗

### [Tag](https://github.com/zircote/swagger-php/tree/master/src/Spec/Tag.php)

Adds metadata to a single tag used by the Operation Object.

#### Nested elements
---
<a href="#externaldocumentation">ExternalDocumentation</a>

#### Parameters
---
- **name** : `string|null`  
  The name of the tag
- **description** : `string|null`  
  A description of the tag (CommonMark syntax)
- **externalDocs** : `ExternalDocumentation|null`  
  Additional external documentation for this tag

#### Reference
---
- [Tag Object](https://spec.openapis.org/oas/v3.1.1.html#tag-object) ↗

### [Xml](https://github.com/zircote/swagger-php/tree/master/src/Spec/Xml.php)

Metadata for XML representation of a schema property.

#### Allowed in
---
<a href="#schema">Schema</a>

#### Parameters
---
- **name** : `string|null`  
  Replaces the name of the element/attribute
- **namespace** : `string|null`  
  The URI of the XML namespace
- **prefix** : `string|null`  
  The namespace prefix to use
- **attribute** : `bool|null`  
  Whether the property translates to an XML attribute
- **wrapped** : `bool|null`  
  Whether array items are wrapped in an additional element

#### Reference
---
- [XML Object](https://spec.openapis.org/oas/v3.1.1.html#xml-object) ↗
