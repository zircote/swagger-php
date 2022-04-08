# Annotations

This page is generated automatically from the `swagger-php` sources.

For improvements head over to [GitHub](https://github.com/zircote/swagger-php) and create a PR ;)

In addition to this page, there are also a number of [examples](https://github.com/zircote/swagger-php/tree/master/Examples#readme) which might help you out.


## [AdditionalProperties](https://github.com/zircote/swagger-php/tree/master/src/Annotations/AdditionalProperties.php)



## [Attachable](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Attachable.php)

A container for custom data to be attached to an annotation.

These will be ignored by `swagger-php` but can be used for custom processing.

## [Components](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Components.php)

Holds a set of reusable objects for different aspects of the OA.

All objects defined within the components object will have no effect on the API unless they are explicitly
referenced from properties outside the components object.

#### Properties
<dl>
  <dt><strong>schemas</strong> : <span style="font-family: monospace;">Schema[]</span></dt>
  <dd><p>Reusable Schemas.</p></dd>
  <dt><strong>responses</strong> : <span style="font-family: monospace;">Response[]</span></dt>
  <dd><p>Reusable Responses.</p></dd>
  <dt><strong>parameters</strong> : <span style="font-family: monospace;">Parameter[]</span></dt>
  <dd><p>Reusable Parameters.</p></dd>
  <dt><strong>examples</strong> : <span style="font-family: monospace;">Examples[]</span></dt>
  <dd><p>Reusable Examples.</p></dd>
  <dt><strong>requestBodies</strong> : <span style="font-family: monospace;">RequestBody[]</span></dt>
  <dd><p>Reusable Request Bodys.</p></dd>
  <dt><strong>headers</strong> : <span style="font-family: monospace;">Header[]</span></dt>
  <dd><p>Reusable Headers.</p></dd>
  <dt><strong>securitySchemes</strong> : <span style="font-family: monospace;">SecurityScheme[]</span></dt>
  <dd><p>Reusable Security Schemes.</p></dd>
  <dt><strong>links</strong> : <span style="font-family: monospace;">Link[]</span></dt>
  <dd><p>Reusable Links.</p></dd>
  <dt><strong>callbacks</strong> : <span style="font-family: monospace;">callable[]</span></dt>
  <dd><p>Reusable Callbacks.</p></dd>
</dl>

#### Reference
- [OAI Components Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#components-object)

## [Contact](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Contact.php)

Contact information for the exposed API.

#### Properties
<dl>
  <dt><strong>name</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The identifying name of the contact person/organization.</p></dd>
  <dt><strong>url</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The URL pointing to the contact information.</p></dd>
  <dt><strong>email</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The email address of the contact person/organization.</p></dd>
</dl>

#### Reference
- [OAI Contact Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#contact-object)

## [Delete](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Delete.php)



#### Properties
<dl>
  <dt><strong>method</strong></dt>
  <dd><p>No details available.</p></dd>
</dl>

## [Discriminator](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Discriminator.php)

The discriminator is a specific object in a schema which is used to inform the consumer of
the specification of an alternative schema based on the value associated with it.

This object is based on the [JSON Schema Specification](http://json-schema.org) and uses a predefined subset of it.
On top of this subset, there are extensions provided by this specification to allow for more complete documentation.

#### Properties
<dl>
  <dt><strong>propertyName</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The name of the property in the payload that will hold the discriminator value.</p></dd>
  <dt><strong>mapping</strong> : <span style="font-family: monospace;">string[]</span></dt>
  <dd><p>An object to hold mappings between payload values and schema names or references.</p></dd>
</dl>

#### Reference
- [OAI Discriminator Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#discriminatorObject)
- [JSON Schema](http://json-schema.org/)

## [Examples](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Examples.php)



#### Properties
<dl>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>No details available.</p><p><i>See</i>: <a href="https://swagger.io/docs/specification/using-ref/">Using refs</a></p></dd>
  <dt><strong>example</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The key into `#/components/examples`.</p></dd>
  <dt><strong>summary</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>Short description for the example.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>Embedded literal example.<br />
<br />
The value field and externalValue field are mutually exclusive.<br />
<br />
To represent examples of media types that cannot naturally be represented<br />
in JSON or YAML, use a string value to contain the example, escaping where necessary.</p></dd>
  <dt><strong>value</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>Embedded literal example.<br />
<br />
The value field and externalValue field are mutually exclusive.<br />
<br />
To represent examples of media types that cannot naturally be represented<br />
in JSON or YAML, use a string value to contain the example, escaping where necessary.</p></dd>
  <dt><strong>externalValue</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>A URL that points to the literal example.<br />
<br />
This provides the capability to reference examples that cannot easily be included<br />
in JSON or YAML documents.<br />
<br />
The value field and externalValue field are mutually exclusive.</p></dd>
</dl>

## [ExternalDocumentation](https://github.com/zircote/swagger-php/tree/master/src/Annotations/ExternalDocumentation.php)

Allows referencing an external resource for extended documentation.

#### Properties
<dl>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>A short description of the target documentation. GFM syntax can be used for rich text representation.</p></dd>
  <dt><strong>url</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The URL for the target documentation.</p></dd>
</dl>

#### Reference
- [OAI External Documentation Object](https://github.com/OAI/OpenAPI-Specification/blob/OpenAPI.next/versions/3.0.md#external-documentation-object)

## [Flow](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Flow.php)

Configuration details for a supported OAuth Flow.

#### Properties
<dl>
  <dt><strong>authorizationUrl</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The authorization url to be used for this flow.<br />
<br />
This must be in the form of a url.</p></dd>
  <dt><strong>tokenUrl</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The token URL to be used for this flow.<br />
<br />
This must be in the form of a url.</p></dd>
  <dt><strong>refreshUrl</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The URL to be used for obtaining refresh tokens.<br />
<br />
This must be in the form of a url.</p></dd>
  <dt><strong>flow</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>Flow name.<br />
<br />
One of ['implicit', 'password', 'authorizationCode', 'clientCredentials'].</p></dd>
  <dt><strong>scopes</strong></dt>
  <dd><p>The available scopes for the OAuth2 security scheme.<br />
<br />
A map between the scope name and a short description for it.</p></dd>
</dl>

#### Reference
- [OAI OAuth Flow Object](https://swagger.io/specification/#oauthFlowObject)

## [Get](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Get.php)



#### Properties
<dl>
  <dt><strong>method</strong></dt>
  <dd><p>No details available.</p></dd>
</dl>

## [Head](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Head.php)



#### Properties
<dl>
  <dt><strong>method</strong></dt>
  <dd><p>No details available.</p></dd>
</dl>

## [Header](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Header.php)



#### Properties
<dl>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>No details available.</p><p><i>See</i>: <a href="https://swagger.io/docs/specification/using-ref/">Using refs</a></p></dd>
  <dt><strong>header</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>A brief description of the parameter.<br />
<br />
This could contain examples of use.<br />
CommonMark syntax MAY be used for rich text representation.</p></dd>
  <dt><strong>required</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>schema</strong> : <span style="font-family: monospace;">\OpenApi\Annotations\Schema</span></dt>
  <dd><p>Schema object.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>Specifies that a parameter is deprecated and SHOULD be transitioned out of usage.</p></dd>
  <dt><strong>allowEmptyValue</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>Sets the ability to pass empty-valued parameters.<br />
<br />
This is valid only for query parameters and allows sending a parameter with an empty value.<br />
<br />
Default value is false.<br />
<br />
If style is used, and if behavior is n/a (cannot be serialized), the value of allowEmptyValue SHALL be ignored.</p></dd>
</dl>

#### Reference
- [OAI Header Object](https://github.com/OAI/OpenAPI-Specification/blob/OpenAPI.next/versions/3.0.md#headerObject).

## [Info](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Info.php)

The object provides metadata about the API.

The metadata may be used by the clients if needed and may be presented in editing or documentation generation tools for convenience.

#### Properties
<dl>
  <dt><strong>title</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The title of the application.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>A short description of the application.<br />
<br />
CommonMark syntax may be used for rich text representation.</p></dd>
  <dt><strong>termsOfService</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>A URL to the Terms of Service for the API.<br />
<br />
Must be in the format of a url.</p></dd>
  <dt><strong>contact</strong> : <span style="font-family: monospace;">Contact</span></dt>
  <dd><p>The contact information for the exposed API.</p></dd>
  <dt><strong>license</strong> : <span style="font-family: monospace;">License</span></dt>
  <dd><p>The license information for the exposed API.</p></dd>
  <dt><strong>version</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The version of the OpenAPI document (which is distinct from the OpenAPI Specification version or the API implementation version).</p></dd>
</dl>

#### Reference
- [OAI Info Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#info-object)

## [Items](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Items.php)

The description of an item in a Schema with type `array`.

## [JsonContent](https://github.com/zircote/swagger-php/tree/master/src/Annotations/JsonContent.php)

Shorthand for a json response.

Use as `@OA\Schema` inside a `Response` and `MediaType`->`'application/json'` will be generated.

#### Properties
<dl>
  <dt><strong>examples</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>No details available.</p></dd>
</dl>

## [License](https://github.com/zircote/swagger-php/tree/master/src/Annotations/License.php)

License information for the exposed API.

#### Properties
<dl>
  <dt><strong>name</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The license name used for the API.</p></dd>
  <dt><strong>identifier</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>An SPDX license expression for the API. The `identifier` field is mutually exclusive of the `url` field.</p></dd>
  <dt><strong>url</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>A URL to the license used for the API. This MUST be in the form of a URL.<br />
<br />
The `url` field is mutually exclusive of the `identifier` field.</p></dd>
</dl>

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
<dl>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>No details available.</p><p><i>See</i>: <a href="https://swagger.io/docs/specification/using-ref/">Using refs</a></p></dd>
  <dt><strong>link</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The key into MediaType->links array.</p></dd>
  <dt><strong>operationRef</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>A relative or absolute reference to an OA operation.<br />
<br />
This field is mutually exclusive of the <code>operationId</code> field, and must point to an Operation object.<br />
<br />
Relative values may be used to locate an existing Operation object in the OpenAPI definition.</p></dd>
  <dt><strong>operationId</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The name of an existing, resolvable OA operation, as defined with a unique <code>operationId</code>.<br />
<br />
This field is mutually exclusive of the <code>operationRef</code> field.</p></dd>
  <dt><strong>parameters</strong></dt>
  <dd><p>A map representing parameters to pass to an operation as specified with operationId or identified via<br />
operationRef.<br />
<br />
The key is the parameter name to be used, whereas the value can be a constant or an expression to<br />
be evaluated and passed to the linked operation.<br />
The parameter name can be qualified using the parameter location [{in}.]{name} for operations<br />
that use the same parameter name in different locations (e.g. path.id).</p></dd>
  <dt><strong>requestBody</strong></dt>
  <dd><p>A literal value or {expression} to use as a request body when calling the target operation.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>A description of the link.<br />
<br />
CommonMark syntax may be used for rich text representation.</p></dd>
  <dt><strong>server</strong> : <span style="font-family: monospace;">Server</span></dt>
  <dd><p>A server object to be used by the target operation.</p></dd>
</dl>

#### Reference
- [OAI Link Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#link-object)

## [MediaType](https://github.com/zircote/swagger-php/tree/master/src/Annotations/MediaType.php)

Each Media Type object provides schema and examples for the media type identified by its key.

#### Properties
<dl>
  <dt><strong>mediaType</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The key into Operation->content array.</p></dd>
  <dt><strong>schema</strong> : <span style="font-family: monospace;">Schema</span></dt>
  <dd><p>The schema defining the type used for the request body.</p></dd>
  <dt><strong>example</strong></dt>
  <dd><p>Example of the media type.<br />
<br />
The example object should be in the correct format as specified by the media type.<br />
The example object is mutually exclusive of the examples object.<br />
<br />
Furthermore, if referencing a schema which contains an example,<br />
the example value shall override the example provided by the schema.</p></dd>
  <dt><strong>examples</strong> : <span style="font-family: monospace;">Examples[]</span></dt>
  <dd><p>Examples of the media type.<br />
<br />
Each example object should match the media type and specified schema if present.<br />
The examples object is mutually exclusive of the example object.<br />
<br />
Furthermore, if referencing a schema which contains an example,<br />
the examples value shall override the example provided by the schema.</p></dd>
  <dt><strong>encoding</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>A map between a property name and its encoding information.<br />
<br />
The key, being the property name, must exist in the schema as a property.<br />
<br />
The encoding object shall only apply to requestBody objects when the media type is multipart or<br />
application/x-www-form-urlencoded.</p></dd>
</dl>

#### Reference
- [OAI Media Type Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#media-type-object)

## [OpenApi](https://github.com/zircote/swagger-php/tree/master/src/Annotations/OpenApi.php)

This is the root document object for the API specification.

#### Properties
<dl>
  <dt><strong>openapi</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The semantic version number of the OpenAPI Specification version that the OpenAPI document uses.<br />
<br />
The openapi field should be used by tooling specifications and clients to interpret the OpenAPI document.<br />
<br />
A version specified via `Generator::setVersion()` will overwrite this value.<br />
<br />
This is not related to the API info::version string.</p></dd>
  <dt><strong>info</strong> : <span style="font-family: monospace;">Info</span></dt>
  <dd><p>Provides metadata about the API. The metadata may be used by tooling as required.</p></dd>
  <dt><strong>servers</strong> : <span style="font-family: monospace;">Server[]</span></dt>
  <dd><p>An array of <code>@OA\Server</code> objects, which provide connectivity information to a target server.<br />
<br />
If not provided, or is an empty array, the default value would be a Server Object with a url value of <code>/</code>.</p></dd>
  <dt><strong>paths</strong> : <span style="font-family: monospace;">PathItem[]</span></dt>
  <dd><p>The available paths and operations for the API.</p></dd>
  <dt><strong>components</strong> : <span style="font-family: monospace;">Components</span></dt>
  <dd><p>An element to hold various components for the specification.</p></dd>
  <dt><strong>security</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>Lists the required security schemes to execute this operation.<br />
<br />
The name used for each property must correspond to a security scheme declared<br />
in the Security Schemes under the Components Object.<br />
Security Requirement Objects that contain multiple schemes require that<br />
all schemes must be satisfied for a request to be authorized.<br />
This enables support for scenarios where multiple query parameters or<br />
HTTP headers are required to convey security information.<br />
When a list of Security Requirement Objects is defined on the Open API object or<br />
Operation Object, only one of Security Requirement Objects in the list needs to<br />
be satisfied to authorize the request.</p></dd>
  <dt><strong>tags</strong> : <span style="font-family: monospace;">Tag[]</span></dt>
  <dd><p>A list of tags used by the specification with additional metadata.<br />
<br />
The order of the tags can be used to reflect on their order by the parsing tools.<br />
Not all tags that are used by the Operation Object must be declared.<br />
The tags that are not declared may be organized randomly or based on the tools' logic.<br />
Each tag name in the list must be unique.</p></dd>
  <dt><strong>externalDocs</strong> : <span style="font-family: monospace;">ExternalDocumentation</span></dt>
  <dd><p>Additional external documentation.</p></dd>
</dl>

#### Reference
- [OAI OpenApi Object](https://github.com/OAI/OpenAPI-Specification/blob/OpenAPI.next/versions/3.0.md#openapi-object)

## [Options](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Options.php)



#### Properties
<dl>
  <dt><strong>method</strong></dt>
  <dd><p>No details available.</p></dd>
</dl>

## [Parameter](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Parameter.php)

Describes a single operation parameter.

A unique parameter is defined by a combination of a name and location.

#### Properties
<dl>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>No details available.</p><p><i>See</i>: <a href="https://swagger.io/docs/specification/using-ref/">Using refs</a></p></dd>
  <dt><strong>parameter</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The key into <code>Components::parameters</code> or <code>PathItem::parameters</code> array.</p></dd>
  <dt><strong>name</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The (case sensitive) name of the parameter.<br />
<br />
If in is "path", the name field must correspond to the associated path segment from the path field in the Paths Object.<br />
<br />
If in is "header" and the name field is "Accept", "Content-Type" or "Authorization", the parameter definition shall be ignored.<br />
For all other cases, the name corresponds to the parameter name used by the in property.</p></dd>
  <dt><strong>in</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The location of the parameter.<br />
<br />
Possible values are "query", "header", "path" or "cookie".</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>A brief description of the parameter.<br />
<br />
This could contain examples of use.<br />
<br />
CommonMark syntax may be used for rich text representation.</p></dd>
  <dt><strong>required</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>Determines whether this parameter is mandatory.<br />
<br />
If the parameter location is "path", this property is required and its value must be true.<br />
Otherwise, the property may be included and its default value is false.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>Specifies that a parameter is deprecated and should be transitioned out of usage.</p></dd>
  <dt><strong>allowEmptyValue</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>Sets the ability to pass empty-valued parameters.<br />
<br />
This is valid only for query parameters and allows sending a parameter with an empty value.<br />
<br />
Default value is false.<br />
<br />
If style is used, and if behavior is n/a (cannot be serialized), the value of allowEmptyValue shall be ignored.</p></dd>
  <dt><strong>style</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>Describes how the parameter value will be serialized depending on the type of the parameter value.<br />
<br />
Default values (based on value of in): for query - form; for path - simple; for header - simple; for cookie - form.</p></dd>
  <dt><strong>explode</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>When this is true, parameter values of type array or object generate separate parameters for each value of the array or key-value pair of the map.<br />
<br />
For other types of parameters this property has no effect.<br />
<br />
When style is form, the default value is true.<br />
For all other styles, the default value is false.</p></dd>
  <dt><strong>allowReserved</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>Determines whether the parameter value should allow reserved characters, as defined by RFC3986 :/?#[]@!$&'()*+,;= to be included without percent-encoding.<br />
<br />
This property only applies to parameters with an in value of query.<br />
<br />
The default value is false.</p></dd>
  <dt><strong>schema</strong> : <span style="font-family: monospace;">Schema</span></dt>
  <dd><p>The schema defining the type used for the parameter.</p></dd>
  <dt><strong>example</strong></dt>
  <dd><p>Example of the media type.<br />
<br />
The example should match the specified schema and encoding properties if present.<br />
The example object is mutually exclusive of the examples object.<br />
Furthermore, if referencing a schema which contains an example, the example value shall override the example provided by the schema.<br />
To represent examples of media types that cannot naturally be represented in JSON or YAML, a string value can contain the example with escaping where necessary.</p></dd>
  <dt><strong>examples</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>Examples of the media type.<br />
<br />
Each example should contain a value in the correct format as specified in the parameter encoding.<br />
The examples object is mutually exclusive of the example object.<br />
Furthermore, if referencing a schema which contains an example, the examples value shall override the example provided by the schema.</p></dd>
  <dt><strong>content</strong> : <span style="font-family: monospace;">MediaType[]</span></dt>
  <dd><p>A map containing the representations for the parameter.<br />
<br />
The key is the media type and the value describes it.<br />
The map must only contain one entry.</p></dd>
  <dt><strong>matrix</strong></dt>
  <dd><p>Path-style parameters defined by RFC6570.</p><p><i>See</i>: <a href="https://tools.ietf.org/html/rfc6570#section-3.2.7">RFC6570</a></p></dd>
  <dt><strong>label</strong></dt>
  <dd><p>Label style parameters defined by RFC6570.</p><p><i>See</i>: <a href="https://tools.ietf.org/html/rfc6570#section-3.2.5">RFC6570</a></p></dd>
  <dt><strong>form</strong></dt>
  <dd><p>Form style parameters defined by RFC6570.<br />
<br />
This option replaces collectionFormat with a csv (when explode is false) or multi (when explode is true) value from OpenAPI 2.0.</p><p><i>See</i>: <a href="https://tools.ietf.org/html/rfc6570#section-3.2.8">RFC6570</a></p></dd>
  <dt><strong>simple</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>Simple style parameters defined by RFC6570.<br />
<br />
This option replaces collectionFormat with a csv value from OpenAPI 2.0.</p><p><i>See</i>: <a href="https://tools.ietf.org/html/rfc6570#section-3.2.2">RFC6570</a></p></dd>
  <dt><strong>spaceDelimited</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>Space separated array values.<br />
<br />
This option replaces collectionFormat equal to ssv from OpenAPI 2.0.</p></dd>
  <dt><strong>pipeDelimited</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>Pipe separated array values.<br />
<br />
This option replaces collectionFormat equal to pipes from OpenAPI 2.0.</p></dd>
  <dt><strong>deepObject</strong></dt>
  <dd><p>Provides a simple way of rendering nested objects using form parameters.</p></dd>
</dl>

#### Reference
- [OAA Parameter Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#parameter-object)

## [Patch](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Patch.php)



#### Properties
<dl>
  <dt><strong>method</strong></dt>
  <dd><p>No details available.</p></dd>
</dl>

## [PathItem](https://github.com/zircote/swagger-php/tree/master/src/Annotations/PathItem.php)

Describes the operations available on a single path.

A Path Item may be empty, due to ACL constraints.
The path itself is still exposed to the documentation viewer but they will not know which operations and parameters are available.

#### Properties
<dl>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>No details available.</p><p><i>See</i>: <a href="https://swagger.io/docs/specification/using-ref/">Using refs</a></p></dd>
  <dt><strong>path</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>key for the Path Object (OpenApi->paths array).</p></dd>
  <dt><strong>summary</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>An optional, string summary, intended to apply to all operations in this path.</p></dd>
  <dt><strong>get</strong> : <span style="font-family: monospace;">Get</span></dt>
  <dd><p>A definition of a GET operation on this path.</p></dd>
  <dt><strong>put</strong> : <span style="font-family: monospace;">Put</span></dt>
  <dd><p>A definition of a PUT operation on this path.</p></dd>
  <dt><strong>post</strong> : <span style="font-family: monospace;">Post</span></dt>
  <dd><p>A definition of a POST operation on this path.</p></dd>
  <dt><strong>delete</strong> : <span style="font-family: monospace;">Delete</span></dt>
  <dd><p>A definition of a DELETE operation on this path.</p></dd>
  <dt><strong>options</strong> : <span style="font-family: monospace;">Options</span></dt>
  <dd><p>A definition of a OPTIONS operation on this path.</p></dd>
  <dt><strong>head</strong> : <span style="font-family: monospace;">Head</span></dt>
  <dd><p>A definition of a HEAD operation on this path.</p></dd>
  <dt><strong>patch</strong> : <span style="font-family: monospace;">Patch</span></dt>
  <dd><p>A definition of a PATCH operation on this path.</p></dd>
  <dt><strong>trace</strong> : <span style="font-family: monospace;">Trace</span></dt>
  <dd><p>A definition of a TRACE operation on this path.</p></dd>
  <dt><strong>servers</strong> : <span style="font-family: monospace;">Server[]</span></dt>
  <dd><p>An alternative server array to service all operations in this path.</p></dd>
  <dt><strong>parameters</strong> : <span style="font-family: monospace;">Parameter[]</span></dt>
  <dd><p>A list of parameters that are applicable for all the operations described under this path.<br />
<br />
These parameters can be overridden at the operation level, but cannot be removed there.<br />
The list must not include duplicated parameters.<br />
A unique parameter is defined by a combination of a name and location.<br />
The list can use the Reference Object to link to parameters that are defined at the OpenAPI Object's components/parameters.</p></dd>
</dl>

#### Reference
- [OAI Path Item Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#path-item-object)

## [PathParameter](https://github.com/zircote/swagger-php/tree/master/src/Annotations/PathParameter.php)

A `@OA\Request` path parameter.

#### Properties
<dl>
  <dt><strong>in</strong></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>required</strong></dt>
  <dd><p>No details available.</p></dd>
</dl>

## [Post](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Post.php)



#### Properties
<dl>
  <dt><strong>method</strong></dt>
  <dd><p>No details available.</p></dd>
</dl>

## [Property](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Property.php)



#### Properties
<dl>
  <dt><strong>property</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The key into Schema->properties array.</p></dd>
</dl>

## [Put](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Put.php)



#### Properties
<dl>
  <dt><strong>method</strong></dt>
  <dd><p>No details available.</p></dd>
</dl>

## [RequestBody](https://github.com/zircote/swagger-php/tree/master/src/Annotations/RequestBody.php)

Describes a single request body.

#### Properties
<dl>
  <dt><strong>ref</strong></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>request</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>Request body model name.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>A brief description of the parameter.<br />
<br />
This could contain examples of use.<br />
<br />
CommonMark syntax may be used for rich text representation.</p></dd>
  <dt><strong>required</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>Determines whether this parameter is mandatory.<br />
<br />
If the parameter location is "path", this property is required and its value must be true.<br />
Otherwise, the property may be included and its default value is false.</p></dd>
  <dt><strong>content</strong> : <span style="font-family: monospace;">array&lt;MediaType&gt;|JsonContent|XmlContent</span></dt>
  <dd><p>The content of the request body.<br />
<br />
The key is a media type or media type range and the value describes it. For requests that match multiple keys,<br />
only the most specific key is applicable. e.g. text/plain overrides text/*.</p></dd>
</dl>

#### Reference
- [OAI Request Body Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#requestBodyObject)

## [Response](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Response.php)

Describes a single response from an API Operation, including design-time,
static links to operations based on the response.

#### Properties
<dl>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>No details available.</p><p><i>See</i>: <a href="https://swagger.io/docs/specification/using-ref/">Using refs</a></p></dd>
  <dt><strong>response</strong> : <span style="font-family: monospace;">string|int</span></dt>
  <dd><p>The key into Operations->responses array.<br />
<br />
A HTTP status code or <code>default</code>.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>A short description of the response.<br />
<br />
CommonMark syntax may be used for rich text representation.</p></dd>
  <dt><strong>headers</strong> : <span style="font-family: monospace;">Header[]</span></dt>
  <dd><p>Maps a header name to its definition.<br />
<br />
RFC7230 states header names are case insensitive.<br />
<br />
If a response header is defined with the name "Content-Type", it shall be ignored.</p><p><i>See</i>: <a href="https://tools.ietf.org/html/rfc7230#page-22">RFC7230</a></p></dd>
  <dt><strong>content</strong> : <span style="font-family: monospace;">MediaType[]</span></dt>
  <dd><p>A map containing descriptions of potential response payloads.<br />
<br />
The key is a media type or media type range and the value describes it.<br />
<br />
For responses that match multiple keys, only the most specific key is applicable;<br />
e.g. <code>text/plain</code> overrides <code>text/*</code>.</p></dd>
  <dt><strong>links</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>A map of operations links that can be followed from the response.<br />
<br />
The key of the map is a short name for the link, following the naming constraints of the names for Component<br />
Objects.</p></dd>
</dl>

#### Reference
- [OAI Response Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#response-object)

## [Schema](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Schema.php)

The definition of input and output data types.

These types can be objects, but also primitives and arrays.

This object is based on the [JSON Schema Specification](http://json-schema.org) and uses a predefined subset of it.
On top of this subset, there are extensions provided by this specification to allow for more complete documentation.

#### Properties
<dl>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>No details available.</p><p><i>See</i>: <a href="https://swagger.io/docs/specification/using-ref/">Using refs</a></p></dd>
  <dt><strong>schema</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The key into Components->schemas array.</p></dd>
  <dt><strong>title</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>Can be used to decorate a user interface with information about the data produced by this user interface.<br />
<br />
Preferably short; use <code>description</code> for more details.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>A description will provide explanation about the purpose of the instance described by this schema.</p></dd>
  <dt><strong>maxProperties</strong> : <span style="font-family: monospace;">int</span></dt>
  <dd><p>An object instance is valid against "maxProperties" if its number of properties is less than, or equal to, the<br />
value of this property.</p></dd>
  <dt><strong>minProperties</strong> : <span style="font-family: monospace;">int</span></dt>
  <dd><p>An object instance is valid against "minProperties" if its number of properties is greater than, or equal to,<br />
the value of this property.</p></dd>
  <dt><strong>required</strong> : <span style="font-family: monospace;">string[]</span></dt>
  <dd><p>An object instance is valid against this property if its property set contains all elements in this property's<br />
array value.</p></dd>
  <dt><strong>properties</strong> : <span style="font-family: monospace;">Property[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>type</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The type of the schema/property. The value MUST be one of "string", "number", "integer", "boolean", "array" or<br />
"object".</p></dd>
  <dt><strong>format</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The extending format for the previously mentioned type. See Data Type Formats for further details.</p></dd>
  <dt><strong>items</strong> : <span style="font-family: monospace;">Items</span></dt>
  <dd><p>Required if type is "array". Describes the type of items in the array.</p></dd>
  <dt><strong>collectionFormat</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>Determines the format of the array if type array is used.<br />
Possible values are:<br />
- csv: comma separated values foo,bar.<br />
- ssv: space separated values foo bar.<br />
- tsv: tab separated values foo\tbar.<br />
- pipes: pipe separated values foo|bar.<br />
- multi: corresponds to multiple parameter instances instead of multiple values for a single instance foo=bar&foo=baz.<br />
This is valid only for parameters of type <code>query</code> or <code>formData</code>.<br />
Default value is csv.</p></dd>
  <dt><strong>default</strong></dt>
  <dd><p>Sets a default value to the parameter. The type of the value depends on the defined type.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor101">JSON schema validation</a></p></dd>
  <dt><strong>maximum</strong> : <span style="font-family: monospace;">number</span></dt>
  <dd><p>No details available.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor17">JSON schema validation</a></p></dd>
  <dt><strong>exclusiveMaximum</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>No details available.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor17">JSON schema validation</a></p></dd>
  <dt><strong>minimum</strong> : <span style="font-family: monospace;">number</span></dt>
  <dd><p>No details available.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor21">JSON schema validation</a></p></dd>
  <dt><strong>exclusiveMinimum</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>No details available.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor21">JSON schema validation</a></p></dd>
  <dt><strong>maxLength</strong> : <span style="font-family: monospace;">int</span></dt>
  <dd><p>No details available.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor26">JSON schema validation</a></p></dd>
  <dt><strong>minLength</strong> : <span style="font-family: monospace;">int</span></dt>
  <dd><p>No details available.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor29">JSON schema validation</a></p></dd>
  <dt><strong>pattern</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>A string instance is considered valid if the regular expression matches the instance successfully.</p></dd>
  <dt><strong>maxItems</strong> : <span style="font-family: monospace;">int</span></dt>
  <dd><p>No details available.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor42">JSON schema validation</a></p></dd>
  <dt><strong>minItems</strong> : <span style="font-family: monospace;">int</span></dt>
  <dd><p>No details available.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor45">JSON schema validation</a></p></dd>
  <dt><strong>uniqueItems</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>No details available.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor49">JSON schema validation</a></p></dd>
  <dt><strong>enum</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>No details available.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor76">JSON schema validation</a></p></dd>
  <dt><strong>multipleOf</strong> : <span style="font-family: monospace;">number</span></dt>
  <dd><p>A numeric instance is valid against "multipleOf" if the result of the division of the instance by this<br />
property's value is an integer.</p></dd>
  <dt><strong>discriminator</strong> : <span style="font-family: monospace;">Discriminator</span></dt>
  <dd><p>Adds support for polymorphism.<br />
<br />
The discriminator is an object name that is used to differentiate between other schemas which may satisfy the<br />
payload description. See Composition and Inheritance for more details.</p></dd>
  <dt><strong>readOnly</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>Declares the property as "read only".<br />
<br />
Relevant only for Schema "properties" definitions.<br />
<br />
This means that it may be sent as part of a response but should not be sent as part of the request.<br />
If the property is marked as readOnly being true and is in the required list, the required will take effect on<br />
the response only. A property must not be marked as both readOnly and writeOnly being true. Default value is<br />
false.</p></dd>
  <dt><strong>writeOnly</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>Declares the property as "write only".<br />
<br />
Relevant only for Schema "properties" definitions.<br />
Therefore, it may be sent as part of a request but should not be sent as part of the response.<br />
If the property is marked as writeOnly being true and is in the required list, the required will take effect on<br />
the request only. A property must not be marked as both readOnly and writeOnly being true. Default value is<br />
false.</p></dd>
  <dt><strong>xml</strong> : <span style="font-family: monospace;">Xml</span></dt>
  <dd><p>This may be used only on properties schemas.<br />
<br />
It has no effect on root schemas.<br />
Adds additional metadata to describe the XML representation of this property.</p></dd>
  <dt><strong>externalDocs</strong> : <span style="font-family: monospace;">ExternalDocumentation</span></dt>
  <dd><p>Additional external documentation for this schema.</p></dd>
  <dt><strong>example</strong></dt>
  <dd><p>A free-form property to include an example of an instance for this schema.<br />
<br />
To represent examples that cannot naturally be represented in JSON or YAML, a string value can be used to<br />
contain the example with escaping where necessary.</p></dd>
  <dt><strong>nullable</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>Allows sending a null value for the defined schema.<br />
Default value is false.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>Specifies that a schema is deprecated and should be transitioned out of usage.<br />
Default value is false.</p></dd>
  <dt><strong>allOf</strong> : <span style="font-family: monospace;">Schema[]</span></dt>
  <dd><p>An instance validates successfully against this property if it validates successfully against all schemas<br />
defined by this property's value.</p></dd>
  <dt><strong>anyOf</strong> : <span style="font-family: monospace;">Schema[]</span></dt>
  <dd><p>An instance validates successfully against this property if it validates successfully against at least one<br />
schema defined by this property's value.</p></dd>
  <dt><strong>oneOf</strong> : <span style="font-family: monospace;">Schema[]</span></dt>
  <dd><p>An instance validates successfully against this property if it validates successfully against exactly one schema<br />
defined by this property's value.</p></dd>
  <dt><strong>not</strong></dt>
  <dd><p>http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.29.</p></dd>
  <dt><strong>additionalProperties</strong> : <span style="font-family: monospace;">bool|AdditionalProperties</span></dt>
  <dd><p>http://json-schema.org/latest/json-schema-validation.html#anchor64.</p></dd>
  <dt><strong>additionalItems</strong></dt>
  <dd><p>http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.10.</p></dd>
  <dt><strong>contains</strong></dt>
  <dd><p>http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.14.</p></dd>
  <dt><strong>patternProperties</strong></dt>
  <dd><p>http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.19.</p></dd>
  <dt><strong>dependencies</strong></dt>
  <dd><p>http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.21.</p></dd>
  <dt><strong>propertyNames</strong></dt>
  <dd><p>http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.22.</p></dd>
  <dt><strong>const</strong></dt>
  <dd><p>http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.24.</p></dd>
</dl>

#### Reference
- [OAI Schema Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#schemaObject)
- [JSON Schema](http://json-schema.org/)

## [SecurityScheme](https://github.com/zircote/swagger-php/tree/master/src/Annotations/SecurityScheme.php)



#### Properties
<dl>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>No details available.</p><p><i>See</i>: <a href="https://swagger.io/docs/specification/using-ref/">Using refs</a></p></dd>
  <dt><strong>securityScheme</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The key into OpenApi->security array.</p></dd>
  <dt><strong>type</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The type of the security scheme.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>A short description for security scheme.</p></dd>
  <dt><strong>name</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The name of the header or query parameter to be used.</p></dd>
  <dt><strong>in</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>Required The location of the API key.</p></dd>
  <dt><strong>flows</strong> : <span style="font-family: monospace;">Flow[]</span></dt>
  <dd><p>The flow used by the OAuth2 security scheme.</p></dd>
  <dt><strong>bearerFormat</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>A hint to the client to identify how the bearer token is formatted.<br />
<br />
Bearer tokens are usually generated by an authorization server, so this information is primarily for documentation purposes.</p></dd>
  <dt><strong>scheme</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The name of the HTTP Authorization scheme.</p><p><i>See</i>: <a href="https://tools.ietf.org/html/rfc7235#section-5.1">RFC7235</a></p></dd>
  <dt><strong>openIdConnectUrl</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>OpenId Connect URL to discover OAuth2 configuration values. This MUST be in the form of a URL.</p></dd>
</dl>

#### Reference
- [OAI Security Scheme Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#securitySchemeObject).

## [Server](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Server.php)

An object representing a server.

#### Properties
<dl>
  <dt><strong>url</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>A URL to the target host.<br />
<br />
This URL supports Server Variables and may be relative,<br />
to indicate that the host location is relative to the location where the OpenAPI document is being served.<br />
Variable substitutions will be made when a variable is named in {brackets}.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>An optional string describing the host designated by the URL.<br />
<br />
CommonMark syntax may be used for rich text representation.</p></dd>
  <dt><strong>variables</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>A map between a variable name and its value.<br />
<br />
The value is used for substitution in the server's URL template.</p></dd>
</dl>

#### Reference
- [OAI Server Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#server-object)

## [ServerVariable](https://github.com/zircote/swagger-php/tree/master/src/Annotations/ServerVariable.php)

An object representing a server variable for server URL template substitution.

#### Properties
<dl>
  <dt><strong>serverVariable</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The key into Server->variables array.</p></dd>
  <dt><strong>enum</strong> : <span style="font-family: monospace;">string[]</span></dt>
  <dd><p>An enumeration of string values to be used if the substitution options are from a limited set.</p></dd>
  <dt><strong>default</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The default value to use for substitution, and to send, if an alternate value is not supplied.<br />
<br />
Unlike the Schema Object's default, this value must be provided by the consumer.</p></dd>
  <dt><strong>variables</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>A map between a variable name and its value.<br />
<br />
The value is used for substitution in the server's URL template.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>An optional description for the server variable.<br />
<br />
CommonMark syntax MAY be used for rich text representation.</p></dd>
</dl>

#### Reference
- [OAI Server Variable Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#server-variable-object)

## [Tag](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Tag.php)



#### Properties
<dl>
  <dt><strong>name</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The name of the tag.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>A short description for the tag. GFM syntax can be used for rich text representation.</p></dd>
  <dt><strong>externalDocs</strong> : <span style="font-family: monospace;">ExternalDocumentation</span></dt>
  <dd><p>Additional external documentation for this tag.</p></dd>
</dl>

#### Reference
- [OAI Tag Object]( https://github.com/OAI/OpenAPI-Specification/blob/OpenAPI.next/versions/3.0.md#tagObject).

## [Trace](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Trace.php)



#### Properties
<dl>
  <dt><strong>method</strong></dt>
  <dd><p>No details available.</p></dd>
</dl>

## [Xml](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Xml.php)



#### Properties
<dl>
  <dt><strong>name</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>Replaces the name of the element/attribute used for the described schema property.<br />
<br />
When defined within the Items Object (items), it will affect the name of the individual XML elements within the list.<br />
When defined alongside type being array (outside the items), it will affect the wrapping element<br />
and only if wrapped is <code>true</code>.<br />
<br />
If wrapped is <code>false</code>, it will be ignored.</p></dd>
  <dt><strong>namespace</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The URL of the namespace definition. Value SHOULD be in the form of a URL.</p></dd>
  <dt><strong>prefix</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The prefix to be used for the name.</p></dd>
  <dt><strong>attribute</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>Declares whether the property definition translates to an attribute instead of an element.<br />
<br />
Default value is <code>false</code>.</p></dd>
  <dt><strong>wrapped</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>MAY be used only for an array definition.<br />
<br />
Signifies whether the array is wrapped (for example  <code>&lt;books>&lt;book/>&lt;book/>&lt;/books></code>)<br />
or unwrapped (<code>&lt;book/>&lt;book/></code>).<br />
<br />
Default value is false. The definition takes effect only when defined alongside type being array (outside the items).</p></dd>
</dl>

#### Reference
- [OAI XML Object](https://github.com/OAI/OpenAPI-Specification/blob/OpenAPI.next/versions/3.0.md#xmlObject).

## [XmlContent](https://github.com/zircote/swagger-php/tree/master/src/Annotations/XmlContent.php)

Shorthand for a xml response.

Use as `@OA\Schema` inside a `Response` and `MediaType`->`'application/xml'` will be generated.

#### Properties
<dl>
  <dt><strong>examples</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>No details available.</p></dd>
</dl>

