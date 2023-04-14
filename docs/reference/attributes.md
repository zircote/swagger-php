# Attributes

This page is generated automatically from the `swagger-php` sources.

For improvements head over to [GitHub](https://github.com/zircote/swagger-php) and create a PR ;)

In addition to this page, there are also a number of [examples](https://github.com/zircote/swagger-php/tree/master/Examples#readme) which might help you out.

## [AdditionalProperties](https://github.com/zircote/swagger-php/tree/master/src/Attributes/AdditionalProperties.php)



#### Allowed in
---
<a href="#schema">Schema</a>, <a href="#property">Property</a>, <a href="#items">Items</a>, <a href="#jsoncontent">JsonContent</a>, <a href="#xmlcontent">XmlContent</a>, <a href="#additionalproperties">AdditionalProperties</a>

#### Nested elements
---
<a href="#discriminator">Discriminator</a>, <a href="#items">Items</a>, <a href="#property">Property</a>, <a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#xml">Xml</a>, <a href="#additionalproperties">AdditionalProperties</a>, <a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">string|class-string|object|null</span></dt>
  <dd><p>The relative or absolute path to the endpoint.</p><p><i>See</i>: <a href="https://swagger.io/docs/specification/using-ref/">Using refs</a></p></dd>
  <dt><strong>schema</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The key into Components->schemas array.</p></dd>
  <dt><strong>title</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Can be used to decorate a user interface with information about the data produced by this user interface.<br />
<br />
Preferably short; use <code>description</code> for more details.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A description will provide explanation about the purpose of the instance described by this schema.</p></dd>
  <dt><strong>maxProperties</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The maximum number of properties allowed in an object instance.<br />
An object instance is valid against this property if its number of properties is less than, or equal to, the value of this attribute.</p></dd>
  <dt><strong>minProperties</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The minimum number of properties allowed in an object instance.<br />
An object instance is valid against this property if its number of properties is greater than, or equal to, the value of this attribute.</p></dd>
  <dt><strong>required</strong> : <span style="font-family: monospace;">string[]</span></dt>
  <dd><p>An object instance is valid against this property if its property set contains all elements in this property's<br />
array value.</p></dd>
  <dt><strong>properties</strong> : <span style="font-family: monospace;">Property[]</span></dt>
  <dd><p>A collection of properties to define for an object. Each property is represented as an instance of the <a href="#property">Property</a> class.</p></dd>
  <dt><strong>type</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The type of the schema/property. The value MUST be one of "string", "number", "integer", "boolean", "array" or<br />
"object".</p></dd>
  <dt><strong>format</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The extending format for the previously mentioned type. See Data Type Formats for further details.</p></dd>
  <dt><strong>items</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Items|null</span></dt>
  <dd><p>Required if type is "array". Describes the type of items in the array.</p></dd>
  <dt><strong>collectionFormat</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Determines the format of the array if type array is used.<br />
Possible values are:<br />
- csv: comma separated values foo,bar.<br />
- ssv: space separated values foo bar.<br />
- tsv: tab separated values foo\tbar.<br />
- pipes: pipe separated values foo|bar.<br />
- multi: corresponds to multiple parameter instances instead of multiple values for a single instance foo=bar&foo=baz.<br />
This is valid only for parameters of type <code>query</code> or <code>formData</code>.<br />
Default value is csv.</p></dd>
  <dt><strong>default</strong> : <span style="font-family: monospace;">mixed|null</span></dt>
  <dd><p>Sets a default value to the parameter. The type of the value depends on the defined type.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor101">JSON schema validation</a></p></dd>
  <dt><strong>maximum</strong> : <span style="font-family: monospace;">int|float</span></dt>
  <dd><p>The maximum value allowed for a numeric property. This value must be a number.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor17">JSON schema validation</a></p></dd>
  <dt><strong>exclusiveMaximum</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>A boolean value indicating whether the maximum value specified in the maximum attribute is excluded from the set of valid values.<br />
If this attribute is set to true, then the maximum value is not included in the set of valid values.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor17">JSON schema validation</a></p></dd>
  <dt><strong>minimum</strong> : <span style="font-family: monospace;">int|float</span></dt>
  <dd><p>The minimum value allowed for a numeric property. This value must be a number.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor21">JSON schema validation</a></p></dd>
  <dt><strong>exclusiveMinimum</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>A boolean value indicating whether the minimum value specified in the minimum attribute is excluded from the set of valid values.<br />
If this attribute is set to true, then the minimum value is not included in the set of valid values.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor21">JSON schema validation</a></p></dd>
  <dt><strong>maxLength</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The maximum length of a string property. A string instance is valid against this property if its length is less than, or equal to, the value of this attribute.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor26">JSON schema validation</a></p></dd>
  <dt><strong>minLength</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The minimum length of a string property. A string instance is valid against this property if its length is greater than, or equal to, the value of this attribute.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor29">JSON schema validation</a></p></dd>
  <dt><strong>maxItems</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The maximum number of items allowed in an array property.<br />
An array instance is valid against this property if its number of items is less than, or equal to, the value of this attribute.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor42">JSON schema validation</a></p></dd>
  <dt><strong>minItems</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The minimum number of items allowed in an array property.<br />
An array instance is valid against this property if its number of items is greater than, or equal to, the value of this attribute.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor45">JSON schema validation</a></p></dd>
  <dt><strong>uniqueItems</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>A boolean value indicating whether all items in an array property must be unique.<br />
If this attribute is set to true, then all items in the array must be unique.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor49">JSON schema validation</a></p></dd>
  <dt><strong>pattern</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A string instance is considered valid if the regular expression matches the instance successfully.</p></dd>
  <dt><strong>enum</strong> : <span style="font-family: monospace;">string[]|int[]|float[]|\UnitEnum[]|class-string</span></dt>
  <dd><p>A collection of allowable values for a property.<br />
A property instance is valid against this attribute if its value is one of the values specified in this collection.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor76">JSON schema validation</a></p></dd>
  <dt><strong>discriminator</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Discriminator|null</span></dt>
  <dd><p>Adds support for polymorphism.<br />
<br />
The discriminator is an object name that is used to differentiate between other schemas which may satisfy the<br />
payload description. See Composition and Inheritance for more details.</p></dd>
  <dt><strong>readOnly</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Declares the property as "read only".<br />
<br />
Relevant only for Schema "properties" definitions.<br />
<br />
This means that it may be sent as part of a response but should not be sent as part of the request.<br />
If the property is marked as readOnly being true and is in the required list, the required will take effect on<br />
the response only. A property must not be marked as both readOnly and writeOnly being true. Default value is<br />
false.</p></dd>
  <dt><strong>writeOnly</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Declares the property as "write only".<br />
<br />
Relevant only for Schema "properties" definitions.<br />
Therefore, it may be sent as part of a request but should not be sent as part of the response.<br />
If the property is marked as writeOnly being true and is in the required list, the required will take effect on<br />
the request only. A property must not be marked as both readOnly and writeOnly being true. Default value is<br />
false.</p></dd>
  <dt><strong>xml</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Xml|null</span></dt>
  <dd><p>This may be used only on properties schemas.<br />
<br />
It has no effect on root schemas.<br />
Adds additional metadata to describe the XML representation of this property.</p></dd>
  <dt><strong>externalDocs</strong> : <span style="font-family: monospace;">OpenApi\Attributes\ExternalDocumentation|null</span></dt>
  <dd><p>Additional external documentation for this schema.</p></dd>
  <dt><strong>example</strong> : <span style="font-family: monospace;">mixed|null</span></dt>
  <dd><p>A free-form property to include an example of an instance for this schema.<br />
<br />
To represent examples that cannot naturally be represented in JSON or YAML, a string value can be used to<br />
contain the example with escaping where necessary.</p></dd>
  <dt><strong>nullable</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Allows sending a null value for the defined schema.<br />
Default value is false.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Specifies that a schema is deprecated and should be transitioned out of usage.<br />
Default value is false.</p></dd>
  <dt><strong>allOf</strong> : <span style="font-family: monospace;">array&lt;Schema|\OpenApi\Annotations\Schema&gt;</span></dt>
  <dd><p>An instance validates successfully against this property if it validates successfully against all schemas<br />
defined by this property's value.</p></dd>
  <dt><strong>anyOf</strong> : <span style="font-family: monospace;">array&lt;Schema|\OpenApi\Annotations\Schema&gt;</span></dt>
  <dd><p>An instance validates successfully against this property if it validates successfully against at least one<br />
schema defined by this property's value.</p></dd>
  <dt><strong>oneOf</strong> : <span style="font-family: monospace;">array&lt;Schema|\OpenApi\Annotations\Schema&gt;</span></dt>
  <dd><p>An instance validates successfully against this property if it validates successfully against exactly one schema<br />
defined by this property's value.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [Attachable](https://github.com/zircote/swagger-php/tree/master/src/Attributes/Attachable.php)



#### Allowed in
---
<a href="#additionalproperties">AdditionalProperties</a>, <a href="#components">Components</a>, <a href="#contact">Contact</a>, <a href="#delete">Delete</a>, <a href="#discriminator">Discriminator</a>, <a href="#examples">Examples</a>, <a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#flow">Flow</a>, <a href="#get">Get</a>, <a href="#head">Head</a>, <a href="#header">Header</a>, <a href="#info">Info</a>, <a href="#items">Items</a>, <a href="#jsoncontent">JsonContent</a>, <a href="#license">License</a>, <a href="#link">Link</a>, <a href="#mediatype">MediaType</a>, <a href="#openapi">OpenApi</a>, <a href="#operation">Operation</a>, <a href="#options">Options</a>, <a href="#parameter">Parameter</a>, <a href="#patch">Patch</a>, <a href="#pathitem">PathItem</a>, <a href="#pathparameter">PathParameter</a>, <a href="#post">Post</a>, <a href="#property">Property</a>, <a href="#put">Put</a>, <a href="#requestbody">RequestBody</a>, <a href="#response">Response</a>, <a href="#schema">Schema</a>, <a href="#securityscheme">SecurityScheme</a>, <a href="#server">Server</a>, <a href="#servervariable">ServerVariable</a>, <a href="#tag">Tag</a>, <a href="#trace">Trace</a>, <a href="#xml">Xml</a>, <a href="#xmlcontent">XmlContent</a>

#### Parameters
---
<dl>
  <dt><strong>properties</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>No details available.</p></dd>
</dl>

## [Components](https://github.com/zircote/swagger-php/tree/master/src/Attributes/Components.php)



#### Allowed in
---
<a href="#openapi">OpenApi</a>

#### Nested elements
---
<a href="#response">Response</a>, <a href="#parameter">Parameter</a>, <a href="#pathparameter">PathParameter</a>, <a href="#requestbody">RequestBody</a>, <a href="#examples">Examples</a>, <a href="#header">Header</a>, <a href="#securityscheme">SecurityScheme</a>, <a href="#link">Link</a>, <a href="#schema">Schema</a>, <a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>schemas</strong> : <span style="font-family: monospace;">array&lt;Schema|\OpenApi\Annotations\Schema&gt;|null</span></dt>
  <dd><p>Reusable Schemas.</p></dd>
  <dt><strong>responses</strong> : <span style="font-family: monospace;">Response[]|null</span></dt>
  <dd><p>Reusable Responses.</p></dd>
  <dt><strong>parameters</strong> : <span style="font-family: monospace;">Parameter[]|null</span></dt>
  <dd><p>Reusable Parameters.</p></dd>
  <dt><strong>requestBodies</strong> : <span style="font-family: monospace;">RequestBody[]|null</span></dt>
  <dd><p>Reusable Request Bodies.</p></dd>
  <dt><strong>examples</strong> : <span style="font-family: monospace;">Examples[]|null</span></dt>
  <dd><p>Reusable Examples.</p></dd>
  <dt><strong>headers</strong> : <span style="font-family: monospace;">Header[]|null</span></dt>
  <dd><p>Reusable Headers.</p></dd>
  <dt><strong>securitySchemes</strong> : <span style="font-family: monospace;">SecurityScheme[]|null</span></dt>
  <dd><p>Reusable Security Schemes.</p></dd>
  <dt><strong>links</strong> : <span style="font-family: monospace;">Link[]|null</span></dt>
  <dd><p>Reusable Links.</p></dd>
  <dt><strong>callbacks</strong> : <span style="font-family: monospace;">array|null</span></dt>
  <dd><p>Reusable Callbacks.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [Contact](https://github.com/zircote/swagger-php/tree/master/src/Attributes/Contact.php)



#### Allowed in
---
<a href="#info">Info</a>

#### Nested elements
---
<a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>name</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The identifying name of the contact person/organization.</p></dd>
  <dt><strong>url</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The URL pointing to the contact information.</p></dd>
  <dt><strong>email</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The email address of the contact person/organization.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [CookieParameter](https://github.com/zircote/swagger-php/tree/master/src/Attributes/CookieParameter.php)



#### Allowed in
---
<a href="#components">Components</a>, <a href="#pathitem">PathItem</a>, <a href="#operation">Operation</a>, <a href="#get">Get</a>, <a href="#post">Post</a>, <a href="#put">Put</a>, <a href="#delete">Delete</a>, <a href="#patch">Patch</a>, <a href="#head">Head</a>, <a href="#options">Options</a>, <a href="#trace">Trace</a>

#### Nested elements
---
<a href="#schema">Schema</a>, <a href="#examples">Examples</a>, <a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>parameter</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The key into <code>Components::parameters</code> or <code>PathItem::parameters</code> array.</p></dd>
  <dt><strong>name</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The (case-sensitive) name of the parameter.<br />
<br />
If in is "path", the name field must correspond to the associated path segment from the path field in the Paths Object.<br />
<br />
If in is "header" and the name field is "Accept", "Content-Type" or "Authorization", the parameter definition shall be ignored.<br />
For all other cases, the name corresponds to the parameter name used by the in property.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A brief description of the parameter.<br />
<br />
This could contain examples of use.<br />
<br />
CommonMark syntax may be used for rich text representation.</p></dd>
  <dt><strong>in</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>This takes 'cookie' as the default location.</p></dd>
  <dt><strong>required</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Determines whether this parameter is mandatory.<br />
<br />
If the parameter location is "path", this property is required and its value must be true.<br />
Otherwise, the property may be included and its default value is false.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Specifies that a parameter is deprecated and should be transitioned out of usage.</p></dd>
  <dt><strong>allowEmptyValue</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Sets the ability to pass empty-valued parameters.<br />
<br />
This is valid only for query parameters and allows sending a parameter with an empty value.<br />
<br />
Default value is false.<br />
<br />
If style is used, and if behavior is n/a (cannot be serialized), the value of allowEmptyValue shall be ignored.</p></dd>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">string|class-string|object|null</span></dt>
  <dd><p>The relative or absolute path to the endpoint.</p><p><i>See</i>: <a href="https://swagger.io/docs/specification/using-ref/">Using refs</a></p></dd>
  <dt><strong>schema</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Schema|null</span></dt>
  <dd><p>The schema defining the type used for the parameter.</p></dd>
  <dt><strong>example</strong> : <span style="font-family: monospace;">mixed|null</span></dt>
  <dd><p>Example of the media type.<br />
<br />
The example should match the specified schema and encoding properties if present.<br />
The example object is mutually exclusive of the examples object.<br />
Furthermore, if referencing a schema which contains an example, the example value shall override the example provided by the schema.<br />
To represent examples of media types that cannot naturally be represented in JSON or YAML, a string value can contain the example with escaping where necessary.</p></dd>
  <dt><strong>examples</strong> : <span style="font-family: monospace;">array&lt;string,Examples&gt;</span></dt>
  <dd><p>Examples of the media type.<br />
<br />
Each example should contain a value in the correct format as specified in the parameter encoding.<br />
The examples object is mutually exclusive of the example object.<br />
Furthermore, if referencing a schema which contains an example, the examples value shall override the example provided by the schema.</p></dd>
  <dt><strong>content</strong> : <span style="font-family: monospace;">array&lt;MediaType&gt;|JsonContent|XmlContent|Attachable|null</span></dt>
  <dd><p>A map containing the representations for the parameter.<br />
<br />
The key is the media type and the value describes it.<br />
The map must only contain one entry.</p></dd>
  <dt><strong>style</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Describes how the parameter value will be serialized depending on the type of the parameter value.<br />
<br />
Default values (based on value of in): for query - form; for path - simple; for header - simple; for cookie - form.</p></dd>
  <dt><strong>explode</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>When this is true, parameter values of type array or object generate separate parameters for each value of the array or key-value pair of the map.<br />
<br />
For other types of parameters this property has no effect.<br />
<br />
When style is form, the default value is true.<br />
For all other styles, the default value is false.</p></dd>
  <dt><strong>allowReserved</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Determines whether the parameter value should allow reserved characters, as defined by RFC3986 :/?#[]@!$&'()*+,;= to be included without percent-encoding.<br />
<br />
This property only applies to parameters with an in value of query.<br />
<br />
The default value is false.</p></dd>
  <dt><strong>spaceDelimited</strong> : <span style="font-family: monospace;">array|null</span></dt>
  <dd><p>Space separated array values.<br />
<br />
This option replaces collectionFormat equal to ssv from OpenAPI 2.0.</p></dd>
  <dt><strong>pipeDelimited</strong> : <span style="font-family: monospace;">array|null</span></dt>
  <dd><p>Pipe separated array values.<br />
<br />
This option replaces collectionFormat equal to pipes from OpenAPI 2.0.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [Delete](https://github.com/zircote/swagger-php/tree/master/src/Attributes/Delete.php)



#### Allowed in
---
<a href="#pathitem">PathItem</a>

#### Nested elements
---
<a href="#parameter">Parameter</a>, <a href="#pathparameter">PathParameter</a>, <a href="#response">Response</a>, <a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#server">Server</a>, <a href="#requestbody">RequestBody</a>, <a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>path</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Key in the OpenApi "Paths Object" for this operation.</p></dd>
  <dt><strong>operationId</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Unique string used to identify the operation.<br />
<br />
The id must be unique among all operations described in the API.<br />
Tools and libraries may use the operationId to uniquely identify an operation, therefore, it is recommended to<br />
follow common programming naming conventions.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A verbose explanation of the operation behavior.<br />
<br />
CommonMark syntax MAY be used for rich text representation.</p></dd>
  <dt><strong>summary</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A short summary of what the operation does.</p></dd>
  <dt><strong>security</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>A declaration of which security mechanisms can be used for this operation.<br />
<br />
The list of values includes alternative security requirement objects that can be used.<br />
<br />
Only one of the security requirement objects need to be satisfied to authorize a request.<br />
<br />
This definition overrides any declared top-level security.<br />
To remove a top-level security declaration, an empty array can be used.</p></dd>
  <dt><strong>servers</strong> : <span style="font-family: monospace;">Server[]</span></dt>
  <dd><p>An alternative server array to service this operation.<br />
<br />
If an alternative server object is specified at the Path Item Object or Root level, it will be overridden by<br />
this value.</p></dd>
  <dt><strong>requestBody</strong> : <span style="font-family: monospace;">OpenApi\Attributes\RequestBody|null</span></dt>
  <dd><p>The request body applicable for this operation.<br />
<br />
The requestBody is only supported in HTTP methods where the HTTP 1.1 specification RFC7231 has explicitly<br />
defined semantics for request bodies. In other cases where the HTTP spec is vague, requestBody shall be ignored<br />
by consumers.</p></dd>
  <dt><strong>tags</strong> : <span style="font-family: monospace;">string[]</span></dt>
  <dd><p>A list of tags for API documentation control.<br />
<br />
Tags can be used for logical grouping of operations by resources or any other qualifier.</p></dd>
  <dt><strong>parameters</strong> : <span style="font-family: monospace;">Parameter[]</span></dt>
  <dd><p>A list of parameters that are applicable for this operation.<br />
<br />
If a parameter is already defined at the Path Item, the new definition will override it but can never remove it.<br />
The list must not include duplicated parameters.<br />
<br />
A unique parameter is defined by a combination of a name and location.<br />
<br />
The list can use the Reference Object to link to parameters that are defined at the OpenAPI Object's<br />
components/parameters.</p></dd>
  <dt><strong>responses</strong> : <span style="font-family: monospace;">Response[]</span></dt>
  <dd><p>The list of possible responses as they are returned from executing this operation.</p></dd>
  <dt><strong>callbacks</strong> : <span style="font-family: monospace;">array|null</span></dt>
  <dd><p>A map of possible out-of band callbacks related to the parent operation.<br />
<br />
The key is a unique identifier for the Callback Object.<br />
<br />
Each value in the map is a Callback Object that describes a request that may be initiated by the API provider<br />
and the expected responses. The key value used to identify the callback object is an expression, evaluated at<br />
runtime, that identifies a URL to use for the callback operation.</p></dd>
  <dt><strong>externalDocs</strong> : <span style="font-family: monospace;">OpenApi\Attributes\ExternalDocumentation|null</span></dt>
  <dd><p>Additional external documentation for this operation.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Declares this operation to be deprecated.<br />
<br />
Consumers should refrain from usage of the declared operation.<br />
<br />
Default value is false.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [Discriminator](https://github.com/zircote/swagger-php/tree/master/src/Attributes/Discriminator.php)



#### Allowed in
---
<a href="#schema">Schema</a>, <a href="#property">Property</a>, <a href="#additionalproperties">AdditionalProperties</a>, <a href="#items">Items</a>, <a href="#jsoncontent">JsonContent</a>, <a href="#xmlcontent">XmlContent</a>

#### Nested elements
---
<a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>propertyName</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The name of the property in the payload that will hold the discriminator value.</p></dd>
  <dt><strong>mapping</strong> : <span style="font-family: monospace;">string[]|null</span></dt>
  <dd><p>An object to hold mappings between payload values and schema names or references.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [Examples](https://github.com/zircote/swagger-php/tree/master/src/Attributes/Examples.php)



#### Allowed in
---
<a href="#components">Components</a>, <a href="#parameter">Parameter</a>, <a href="#pathparameter">PathParameter</a>, <a href="#mediatype">MediaType</a>, <a href="#jsoncontent">JsonContent</a>, <a href="#xmlcontent">XmlContent</a>

#### Nested elements
---
<a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>example</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The key into `#/components/examples`.</p></dd>
  <dt><strong>summary</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Short description for the example.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Embedded literal example.<br />
<br />
The value field and externalValue field are mutually exclusive.<br />
<br />
To represent examples of media types that cannot naturally be represented<br />
in JSON or YAML, use a string value to contain the example, escaping where necessary.</p></dd>
  <dt><strong>value</strong> : <span style="font-family: monospace;">array|string|int|null</span></dt>
  <dd><p>Embedded literal example.<br />
<br />
The value field and externalValue field are mutually exclusive.<br />
<br />
To represent examples of media types that cannot naturally be represented<br />
in JSON or YAML, use a string value to contain the example, escaping where necessary.</p></dd>
  <dt><strong>externalValue</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>An URL that points to the literal example.<br />
<br />
This provides the capability to reference examples that cannot easily be included<br />
in JSON or YAML documents.<br />
<br />
The value field and externalValue field are mutually exclusive.</p></dd>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">string|class-string|object|null</span></dt>
  <dd><p>The relative or absolute path to an example.</p><p><i>See</i>: <a href="https://swagger.io/docs/specification/using-ref/">Using refs</a></p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [ExternalDocumentation](https://github.com/zircote/swagger-php/tree/master/src/Attributes/ExternalDocumentation.php)



#### Allowed in
---
<a href="#openapi">OpenApi</a>, <a href="#tag">Tag</a>, <a href="#schema">Schema</a>, <a href="#additionalproperties">AdditionalProperties</a>, <a href="#property">Property</a>, <a href="#operation">Operation</a>, <a href="#get">Get</a>, <a href="#post">Post</a>, <a href="#put">Put</a>, <a href="#delete">Delete</a>, <a href="#patch">Patch</a>, <a href="#head">Head</a>, <a href="#options">Options</a>, <a href="#trace">Trace</a>, <a href="#items">Items</a>, <a href="#jsoncontent">JsonContent</a>, <a href="#xmlcontent">XmlContent</a>

#### Nested elements
---
<a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A short description of the target documentation. GFM syntax can be used for rich text representation.</p></dd>
  <dt><strong>url</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The URL for the target documentation.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [Flow](https://github.com/zircote/swagger-php/tree/master/src/Attributes/Flow.php)



#### Allowed in
---
<a href="#securityscheme">SecurityScheme</a>

#### Nested elements
---
<a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>authorizationUrl</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The authorization url to be used for this flow.<br />
<br />
This must be in the form of an url.</p></dd>
  <dt><strong>tokenUrl</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The token URL to be used for this flow.<br />
<br />
This must be in the form of an url.</p></dd>
  <dt><strong>refreshUrl</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The URL to be used for obtaining refresh tokens.<br />
<br />
This must be in the form of an url.</p></dd>
  <dt><strong>flow</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Flow name.<br />
<br />
One of ['implicit', 'password', 'authorizationCode', 'clientCredentials'].</p></dd>
  <dt><strong>scopes</strong> : <span style="font-family: monospace;">array|null</span></dt>
  <dd><p>The available scopes for the OAuth2 security scheme.<br />
<br />
A map between the scope name and a short description for it.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [Get](https://github.com/zircote/swagger-php/tree/master/src/Attributes/Get.php)



#### Allowed in
---
<a href="#pathitem">PathItem</a>

#### Nested elements
---
<a href="#parameter">Parameter</a>, <a href="#pathparameter">PathParameter</a>, <a href="#response">Response</a>, <a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#server">Server</a>, <a href="#requestbody">RequestBody</a>, <a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>path</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Key in the OpenApi "Paths Object" for this operation.</p></dd>
  <dt><strong>operationId</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Unique string used to identify the operation.<br />
<br />
The id must be unique among all operations described in the API.<br />
Tools and libraries may use the operationId to uniquely identify an operation, therefore, it is recommended to<br />
follow common programming naming conventions.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A verbose explanation of the operation behavior.<br />
<br />
CommonMark syntax MAY be used for rich text representation.</p></dd>
  <dt><strong>summary</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A short summary of what the operation does.</p></dd>
  <dt><strong>security</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>A declaration of which security mechanisms can be used for this operation.<br />
<br />
The list of values includes alternative security requirement objects that can be used.<br />
<br />
Only one of the security requirement objects need to be satisfied to authorize a request.<br />
<br />
This definition overrides any declared top-level security.<br />
To remove a top-level security declaration, an empty array can be used.</p></dd>
  <dt><strong>servers</strong> : <span style="font-family: monospace;">Server[]</span></dt>
  <dd><p>An alternative server array to service this operation.<br />
<br />
If an alternative server object is specified at the Path Item Object or Root level, it will be overridden by<br />
this value.</p></dd>
  <dt><strong>requestBody</strong> : <span style="font-family: monospace;">OpenApi\Attributes\RequestBody|null</span></dt>
  <dd><p>The request body applicable for this operation.<br />
<br />
The requestBody is only supported in HTTP methods where the HTTP 1.1 specification RFC7231 has explicitly<br />
defined semantics for request bodies. In other cases where the HTTP spec is vague, requestBody shall be ignored<br />
by consumers.</p></dd>
  <dt><strong>tags</strong> : <span style="font-family: monospace;">string[]</span></dt>
  <dd><p>A list of tags for API documentation control.<br />
<br />
Tags can be used for logical grouping of operations by resources or any other qualifier.</p></dd>
  <dt><strong>parameters</strong> : <span style="font-family: monospace;">Parameter[]</span></dt>
  <dd><p>A list of parameters that are applicable for this operation.<br />
<br />
If a parameter is already defined at the Path Item, the new definition will override it but can never remove it.<br />
The list must not include duplicated parameters.<br />
<br />
A unique parameter is defined by a combination of a name and location.<br />
<br />
The list can use the Reference Object to link to parameters that are defined at the OpenAPI Object's<br />
components/parameters.</p></dd>
  <dt><strong>responses</strong> : <span style="font-family: monospace;">Response[]</span></dt>
  <dd><p>The list of possible responses as they are returned from executing this operation.</p></dd>
  <dt><strong>callbacks</strong> : <span style="font-family: monospace;">array|null</span></dt>
  <dd><p>A map of possible out-of band callbacks related to the parent operation.<br />
<br />
The key is a unique identifier for the Callback Object.<br />
<br />
Each value in the map is a Callback Object that describes a request that may be initiated by the API provider<br />
and the expected responses. The key value used to identify the callback object is an expression, evaluated at<br />
runtime, that identifies a URL to use for the callback operation.</p></dd>
  <dt><strong>externalDocs</strong> : <span style="font-family: monospace;">OpenApi\Attributes\ExternalDocumentation|null</span></dt>
  <dd><p>Additional external documentation for this operation.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Declares this operation to be deprecated.<br />
<br />
Consumers should refrain from usage of the declared operation.<br />
<br />
Default value is false.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [Head](https://github.com/zircote/swagger-php/tree/master/src/Attributes/Head.php)



#### Allowed in
---
<a href="#pathitem">PathItem</a>

#### Nested elements
---
<a href="#parameter">Parameter</a>, <a href="#pathparameter">PathParameter</a>, <a href="#response">Response</a>, <a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#server">Server</a>, <a href="#requestbody">RequestBody</a>, <a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>path</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Key in the OpenApi "Paths Object" for this operation.</p></dd>
  <dt><strong>operationId</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Unique string used to identify the operation.<br />
<br />
The id must be unique among all operations described in the API.<br />
Tools and libraries may use the operationId to uniquely identify an operation, therefore, it is recommended to<br />
follow common programming naming conventions.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A verbose explanation of the operation behavior.<br />
<br />
CommonMark syntax MAY be used for rich text representation.</p></dd>
  <dt><strong>summary</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A short summary of what the operation does.</p></dd>
  <dt><strong>security</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>A declaration of which security mechanisms can be used for this operation.<br />
<br />
The list of values includes alternative security requirement objects that can be used.<br />
<br />
Only one of the security requirement objects need to be satisfied to authorize a request.<br />
<br />
This definition overrides any declared top-level security.<br />
To remove a top-level security declaration, an empty array can be used.</p></dd>
  <dt><strong>servers</strong> : <span style="font-family: monospace;">Server[]</span></dt>
  <dd><p>An alternative server array to service this operation.<br />
<br />
If an alternative server object is specified at the Path Item Object or Root level, it will be overridden by<br />
this value.</p></dd>
  <dt><strong>requestBody</strong> : <span style="font-family: monospace;">OpenApi\Attributes\RequestBody|null</span></dt>
  <dd><p>The request body applicable for this operation.<br />
<br />
The requestBody is only supported in HTTP methods where the HTTP 1.1 specification RFC7231 has explicitly<br />
defined semantics for request bodies. In other cases where the HTTP spec is vague, requestBody shall be ignored<br />
by consumers.</p></dd>
  <dt><strong>tags</strong> : <span style="font-family: monospace;">string[]</span></dt>
  <dd><p>A list of tags for API documentation control.<br />
<br />
Tags can be used for logical grouping of operations by resources or any other qualifier.</p></dd>
  <dt><strong>parameters</strong> : <span style="font-family: monospace;">Parameter[]</span></dt>
  <dd><p>A list of parameters that are applicable for this operation.<br />
<br />
If a parameter is already defined at the Path Item, the new definition will override it but can never remove it.<br />
The list must not include duplicated parameters.<br />
<br />
A unique parameter is defined by a combination of a name and location.<br />
<br />
The list can use the Reference Object to link to parameters that are defined at the OpenAPI Object's<br />
components/parameters.</p></dd>
  <dt><strong>responses</strong> : <span style="font-family: monospace;">Response[]</span></dt>
  <dd><p>The list of possible responses as they are returned from executing this operation.</p></dd>
  <dt><strong>callbacks</strong> : <span style="font-family: monospace;">array|null</span></dt>
  <dd><p>A map of possible out-of band callbacks related to the parent operation.<br />
<br />
The key is a unique identifier for the Callback Object.<br />
<br />
Each value in the map is a Callback Object that describes a request that may be initiated by the API provider<br />
and the expected responses. The key value used to identify the callback object is an expression, evaluated at<br />
runtime, that identifies a URL to use for the callback operation.</p></dd>
  <dt><strong>externalDocs</strong> : <span style="font-family: monospace;">OpenApi\Attributes\ExternalDocumentation|null</span></dt>
  <dd><p>Additional external documentation for this operation.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Declares this operation to be deprecated.<br />
<br />
Consumers should refrain from usage of the declared operation.<br />
<br />
Default value is false.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [Header](https://github.com/zircote/swagger-php/tree/master/src/Attributes/Header.php)



#### Allowed in
---
<a href="#components">Components</a>, <a href="#response">Response</a>

#### Nested elements
---
<a href="#schema">Schema</a>, <a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">string|class-string|object|null</span></dt>
  <dd><p>The relative or absolute path to the endpoint.</p><p><i>See</i>: <a href="https://swagger.io/docs/specification/using-ref/">Using refs</a></p></dd>
  <dt><strong>header</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A brief description of the parameter.<br />
<br />
This could contain examples of use.<br />
CommonMark syntax MAY be used for rich text representation.</p></dd>
  <dt><strong>required</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>schema</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Schema|null</span></dt>
  <dd><p>Schema object.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Specifies that a parameter is deprecated and SHOULD be transitioned out of usage.</p></dd>
  <dt><strong>allowEmptyValue</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Sets the ability to pass empty-valued parameters.<br />
<br />
This is valid only for query parameters and allows sending a parameter with an empty value.<br />
<br />
Default value is false.<br />
<br />
If style is used, and if behavior is n/a (cannot be serialized), the value of allowEmptyValue SHALL be ignored.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [HeaderParameter](https://github.com/zircote/swagger-php/tree/master/src/Attributes/HeaderParameter.php)



#### Allowed in
---
<a href="#components">Components</a>, <a href="#pathitem">PathItem</a>, <a href="#operation">Operation</a>, <a href="#get">Get</a>, <a href="#post">Post</a>, <a href="#put">Put</a>, <a href="#delete">Delete</a>, <a href="#patch">Patch</a>, <a href="#head">Head</a>, <a href="#options">Options</a>, <a href="#trace">Trace</a>

#### Nested elements
---
<a href="#schema">Schema</a>, <a href="#examples">Examples</a>, <a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>parameter</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The key into <code>Components::parameters</code> or <code>PathItem::parameters</code> array.</p></dd>
  <dt><strong>name</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The (case-sensitive) name of the parameter.<br />
<br />
If in is "path", the name field must correspond to the associated path segment from the path field in the Paths Object.<br />
<br />
If in is "header" and the name field is "Accept", "Content-Type" or "Authorization", the parameter definition shall be ignored.<br />
For all other cases, the name corresponds to the parameter name used by the in property.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A brief description of the parameter.<br />
<br />
This could contain examples of use.<br />
<br />
CommonMark syntax may be used for rich text representation.</p></dd>
  <dt><strong>in</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>This takes 'header' as the default location.</p></dd>
  <dt><strong>required</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Determines whether this parameter is mandatory.<br />
<br />
If the parameter location is "path", this property is required and its value must be true.<br />
Otherwise, the property may be included and its default value is false.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Specifies that a parameter is deprecated and should be transitioned out of usage.</p></dd>
  <dt><strong>allowEmptyValue</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Sets the ability to pass empty-valued parameters.<br />
<br />
This is valid only for query parameters and allows sending a parameter with an empty value.<br />
<br />
Default value is false.<br />
<br />
If style is used, and if behavior is n/a (cannot be serialized), the value of allowEmptyValue shall be ignored.</p></dd>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">string|class-string|object|null</span></dt>
  <dd><p>The relative or absolute path to the endpoint.</p><p><i>See</i>: <a href="https://swagger.io/docs/specification/using-ref/">Using refs</a></p></dd>
  <dt><strong>schema</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Schema|null</span></dt>
  <dd><p>The schema defining the type used for the parameter.</p></dd>
  <dt><strong>example</strong> : <span style="font-family: monospace;">mixed|null</span></dt>
  <dd><p>Example of the media type.<br />
<br />
The example should match the specified schema and encoding properties if present.<br />
The example object is mutually exclusive of the examples object.<br />
Furthermore, if referencing a schema which contains an example, the example value shall override the example provided by the schema.<br />
To represent examples of media types that cannot naturally be represented in JSON or YAML, a string value can contain the example with escaping where necessary.</p></dd>
  <dt><strong>examples</strong> : <span style="font-family: monospace;">array&lt;string,Examples&gt;</span></dt>
  <dd><p>Examples of the media type.<br />
<br />
Each example should contain a value in the correct format as specified in the parameter encoding.<br />
The examples object is mutually exclusive of the example object.<br />
Furthermore, if referencing a schema which contains an example, the examples value shall override the example provided by the schema.</p></dd>
  <dt><strong>content</strong> : <span style="font-family: monospace;">array&lt;MediaType&gt;|JsonContent|XmlContent|Attachable|null</span></dt>
  <dd><p>A map containing the representations for the parameter.<br />
<br />
The key is the media type and the value describes it.<br />
The map must only contain one entry.</p></dd>
  <dt><strong>style</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Describes how the parameter value will be serialized depending on the type of the parameter value.<br />
<br />
Default values (based on value of in): for query - form; for path - simple; for header - simple; for cookie - form.</p></dd>
  <dt><strong>explode</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>When this is true, parameter values of type array or object generate separate parameters for each value of the array or key-value pair of the map.<br />
<br />
For other types of parameters this property has no effect.<br />
<br />
When style is form, the default value is true.<br />
For all other styles, the default value is false.</p></dd>
  <dt><strong>allowReserved</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Determines whether the parameter value should allow reserved characters, as defined by RFC3986 :/?#[]@!$&'()*+,;= to be included without percent-encoding.<br />
<br />
This property only applies to parameters with an in value of query.<br />
<br />
The default value is false.</p></dd>
  <dt><strong>spaceDelimited</strong> : <span style="font-family: monospace;">array|null</span></dt>
  <dd><p>Space separated array values.<br />
<br />
This option replaces collectionFormat equal to ssv from OpenAPI 2.0.</p></dd>
  <dt><strong>pipeDelimited</strong> : <span style="font-family: monospace;">array|null</span></dt>
  <dd><p>Pipe separated array values.<br />
<br />
This option replaces collectionFormat equal to pipes from OpenAPI 2.0.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [Info](https://github.com/zircote/swagger-php/tree/master/src/Attributes/Info.php)



#### Allowed in
---
<a href="#openapi">OpenApi</a>

#### Nested elements
---
<a href="#contact">Contact</a>, <a href="#license">License</a>, <a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>version</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The version of the OpenAPI document (which is distinct from the OpenAPI Specification version or the API implementation version).</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A short description of the application.<br />
<br />
CommonMark syntax may be used for rich text representation.</p></dd>
  <dt><strong>title</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The title of the application.</p></dd>
  <dt><strong>termsOfService</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>An URL to the Terms of Service for the API.<br />
<br />
Must be in the format of an url.</p></dd>
  <dt><strong>contact</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Contact|null</span></dt>
  <dd><p>The contact information for the exposed API.</p></dd>
  <dt><strong>license</strong> : <span style="font-family: monospace;">OpenApi\Attributes\License|null</span></dt>
  <dd><p>The license information for the exposed API.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [Items](https://github.com/zircote/swagger-php/tree/master/src/Attributes/Items.php)



#### Allowed in
---
<a href="#property">Property</a>, <a href="#additionalproperties">AdditionalProperties</a>, <a href="#schema">Schema</a>, <a href="#jsoncontent">JsonContent</a>, <a href="#xmlcontent">XmlContent</a>, <a href="#items">Items</a>

#### Nested elements
---
<a href="#discriminator">Discriminator</a>, <a href="#items">Items</a>, <a href="#property">Property</a>, <a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#xml">Xml</a>, <a href="#additionalproperties">AdditionalProperties</a>, <a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">string|class-string|object|null</span></dt>
  <dd><p>The relative or absolute path to the endpoint.</p><p><i>See</i>: <a href="https://swagger.io/docs/specification/using-ref/">Using refs</a></p></dd>
  <dt><strong>schema</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The key into Components->schemas array.</p></dd>
  <dt><strong>title</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Can be used to decorate a user interface with information about the data produced by this user interface.<br />
<br />
Preferably short; use <code>description</code> for more details.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A description will provide explanation about the purpose of the instance described by this schema.</p></dd>
  <dt><strong>maxProperties</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The maximum number of properties allowed in an object instance.<br />
An object instance is valid against this property if its number of properties is less than, or equal to, the value of this attribute.</p></dd>
  <dt><strong>minProperties</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The minimum number of properties allowed in an object instance.<br />
An object instance is valid against this property if its number of properties is greater than, or equal to, the value of this attribute.</p></dd>
  <dt><strong>required</strong> : <span style="font-family: monospace;">string[]</span></dt>
  <dd><p>An object instance is valid against this property if its property set contains all elements in this property's<br />
array value.</p></dd>
  <dt><strong>properties</strong> : <span style="font-family: monospace;">Property[]</span></dt>
  <dd><p>A collection of properties to define for an object. Each property is represented as an instance of the <a href="#property">Property</a> class.</p></dd>
  <dt><strong>type</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The type of the schema/property. The value MUST be one of "string", "number", "integer", "boolean", "array" or<br />
"object".</p></dd>
  <dt><strong>format</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The extending format for the previously mentioned type. See Data Type Formats for further details.</p></dd>
  <dt><strong>items</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Items|null</span></dt>
  <dd><p>Required if type is "array". Describes the type of items in the array.</p></dd>
  <dt><strong>collectionFormat</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Determines the format of the array if type array is used.<br />
Possible values are:<br />
- csv: comma separated values foo,bar.<br />
- ssv: space separated values foo bar.<br />
- tsv: tab separated values foo\tbar.<br />
- pipes: pipe separated values foo|bar.<br />
- multi: corresponds to multiple parameter instances instead of multiple values for a single instance foo=bar&foo=baz.<br />
This is valid only for parameters of type <code>query</code> or <code>formData</code>.<br />
Default value is csv.</p></dd>
  <dt><strong>default</strong> : <span style="font-family: monospace;">mixed|null</span></dt>
  <dd><p>Sets a default value to the parameter. The type of the value depends on the defined type.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor101">JSON schema validation</a></p></dd>
  <dt><strong>maximum</strong> : <span style="font-family: monospace;">int|float</span></dt>
  <dd><p>The maximum value allowed for a numeric property. This value must be a number.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor17">JSON schema validation</a></p></dd>
  <dt><strong>exclusiveMaximum</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>A boolean value indicating whether the maximum value specified in the maximum attribute is excluded from the set of valid values.<br />
If this attribute is set to true, then the maximum value is not included in the set of valid values.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor17">JSON schema validation</a></p></dd>
  <dt><strong>minimum</strong> : <span style="font-family: monospace;">int|float</span></dt>
  <dd><p>The minimum value allowed for a numeric property. This value must be a number.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor21">JSON schema validation</a></p></dd>
  <dt><strong>exclusiveMinimum</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>A boolean value indicating whether the minimum value specified in the minimum attribute is excluded from the set of valid values.<br />
If this attribute is set to true, then the minimum value is not included in the set of valid values.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor21">JSON schema validation</a></p></dd>
  <dt><strong>maxLength</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The maximum length of a string property. A string instance is valid against this property if its length is less than, or equal to, the value of this attribute.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor26">JSON schema validation</a></p></dd>
  <dt><strong>minLength</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The minimum length of a string property. A string instance is valid against this property if its length is greater than, or equal to, the value of this attribute.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor29">JSON schema validation</a></p></dd>
  <dt><strong>maxItems</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The maximum number of items allowed in an array property.<br />
An array instance is valid against this property if its number of items is less than, or equal to, the value of this attribute.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor42">JSON schema validation</a></p></dd>
  <dt><strong>minItems</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The minimum number of items allowed in an array property.<br />
An array instance is valid against this property if its number of items is greater than, or equal to, the value of this attribute.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor45">JSON schema validation</a></p></dd>
  <dt><strong>uniqueItems</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>A boolean value indicating whether all items in an array property must be unique.<br />
If this attribute is set to true, then all items in the array must be unique.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor49">JSON schema validation</a></p></dd>
  <dt><strong>pattern</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A string instance is considered valid if the regular expression matches the instance successfully.</p></dd>
  <dt><strong>enum</strong> : <span style="font-family: monospace;">string[]|int[]|float[]|\UnitEnum[]|class-string</span></dt>
  <dd><p>A collection of allowable values for a property.<br />
A property instance is valid against this attribute if its value is one of the values specified in this collection.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor76">JSON schema validation</a></p></dd>
  <dt><strong>discriminator</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Discriminator|null</span></dt>
  <dd><p>Adds support for polymorphism.<br />
<br />
The discriminator is an object name that is used to differentiate between other schemas which may satisfy the<br />
payload description. See Composition and Inheritance for more details.</p></dd>
  <dt><strong>readOnly</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Declares the property as "read only".<br />
<br />
Relevant only for Schema "properties" definitions.<br />
<br />
This means that it may be sent as part of a response but should not be sent as part of the request.<br />
If the property is marked as readOnly being true and is in the required list, the required will take effect on<br />
the response only. A property must not be marked as both readOnly and writeOnly being true. Default value is<br />
false.</p></dd>
  <dt><strong>writeOnly</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Declares the property as "write only".<br />
<br />
Relevant only for Schema "properties" definitions.<br />
Therefore, it may be sent as part of a request but should not be sent as part of the response.<br />
If the property is marked as writeOnly being true and is in the required list, the required will take effect on<br />
the request only. A property must not be marked as both readOnly and writeOnly being true. Default value is<br />
false.</p></dd>
  <dt><strong>xml</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Xml|null</span></dt>
  <dd><p>This may be used only on properties schemas.<br />
<br />
It has no effect on root schemas.<br />
Adds additional metadata to describe the XML representation of this property.</p></dd>
  <dt><strong>externalDocs</strong> : <span style="font-family: monospace;">OpenApi\Attributes\ExternalDocumentation|null</span></dt>
  <dd><p>Additional external documentation for this schema.</p></dd>
  <dt><strong>example</strong> : <span style="font-family: monospace;">mixed|null</span></dt>
  <dd><p>A free-form property to include an example of an instance for this schema.<br />
<br />
To represent examples that cannot naturally be represented in JSON or YAML, a string value can be used to<br />
contain the example with escaping where necessary.</p></dd>
  <dt><strong>nullable</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Allows sending a null value for the defined schema.<br />
Default value is false.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Specifies that a schema is deprecated and should be transitioned out of usage.<br />
Default value is false.</p></dd>
  <dt><strong>allOf</strong> : <span style="font-family: monospace;">array&lt;Schema|\OpenApi\Annotations\Schema&gt;</span></dt>
  <dd><p>An instance validates successfully against this property if it validates successfully against all schemas<br />
defined by this property's value.</p></dd>
  <dt><strong>anyOf</strong> : <span style="font-family: monospace;">array&lt;Schema|\OpenApi\Annotations\Schema&gt;</span></dt>
  <dd><p>An instance validates successfully against this property if it validates successfully against at least one<br />
schema defined by this property's value.</p></dd>
  <dt><strong>oneOf</strong> : <span style="font-family: monospace;">array&lt;Schema|\OpenApi\Annotations\Schema&gt;</span></dt>
  <dd><p>An instance validates successfully against this property if it validates successfully against exactly one schema<br />
defined by this property's value.</p></dd>
  <dt><strong>additionalProperties</strong> : <span style="font-family: monospace;">OpenApi\Attributes\AdditionalProperties|bool|null</span></dt>
  <dd><p>http://json-schema.org/latest/json-schema-validation.html#anchor64.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [JsonContent](https://github.com/zircote/swagger-php/tree/master/src/Attributes/JsonContent.php)



#### Nested elements
---
<a href="#discriminator">Discriminator</a>, <a href="#items">Items</a>, <a href="#property">Property</a>, <a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#additionalproperties">AdditionalProperties</a>, <a href="#examples">Examples</a>, <a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>examples</strong> : <span style="font-family: monospace;">array&lt;string,Examples&gt;</span></dt>
  <dd><p>An associative array of Examples attributes. The keys represent the name of the example and the values are instances of the Examples attribute.<br />
Each example is used to show how the content of the request or response should look like.</p></dd>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">string|class-string|object|null</span></dt>
  <dd><p>The relative or absolute path to the endpoint.</p><p><i>See</i>: <a href="https://swagger.io/docs/specification/using-ref/">Using refs</a></p></dd>
  <dt><strong>schema</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The key into Components->schemas array.</p></dd>
  <dt><strong>title</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Can be used to decorate a user interface with information about the data produced by this user interface.<br />
<br />
Preferably short; use <code>description</code> for more details.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A description will provide explanation about the purpose of the instance described by this schema.</p></dd>
  <dt><strong>maxProperties</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The maximum number of properties allowed in an object instance.<br />
An object instance is valid against this property if its number of properties is less than, or equal to, the value of this attribute.</p></dd>
  <dt><strong>minProperties</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The minimum number of properties allowed in an object instance.<br />
An object instance is valid against this property if its number of properties is greater than, or equal to, the value of this attribute.</p></dd>
  <dt><strong>required</strong> : <span style="font-family: monospace;">string[]</span></dt>
  <dd><p>An object instance is valid against this property if its property set contains all elements in this property's<br />
array value.</p></dd>
  <dt><strong>properties</strong> : <span style="font-family: monospace;">Property[]</span></dt>
  <dd><p>A collection of properties to define for an object. Each property is represented as an instance of the <a href="#property">Property</a> class.</p></dd>
  <dt><strong>type</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The type of the schema/property. The value MUST be one of "string", "number", "integer", "boolean", "array" or<br />
"object".</p></dd>
  <dt><strong>format</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The extending format for the previously mentioned type. See Data Type Formats for further details.</p></dd>
  <dt><strong>items</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Items|null</span></dt>
  <dd><p>Required if type is "array". Describes the type of items in the array.</p></dd>
  <dt><strong>collectionFormat</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Determines the format of the array if type array is used.<br />
Possible values are:<br />
- csv: comma separated values foo,bar.<br />
- ssv: space separated values foo bar.<br />
- tsv: tab separated values foo\tbar.<br />
- pipes: pipe separated values foo|bar.<br />
- multi: corresponds to multiple parameter instances instead of multiple values for a single instance foo=bar&foo=baz.<br />
This is valid only for parameters of type <code>query</code> or <code>formData</code>.<br />
Default value is csv.</p></dd>
  <dt><strong>default</strong> : <span style="font-family: monospace;">mixed|null</span></dt>
  <dd><p>Sets a default value to the parameter. The type of the value depends on the defined type.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor101">JSON schema validation</a></p></dd>
  <dt><strong>maximum</strong> : <span style="font-family: monospace;">int|float</span></dt>
  <dd><p>The maximum value allowed for a numeric property. This value must be a number.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor17">JSON schema validation</a></p></dd>
  <dt><strong>exclusiveMaximum</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>A boolean value indicating whether the maximum value specified in the maximum attribute is excluded from the set of valid values.<br />
If this attribute is set to true, then the maximum value is not included in the set of valid values.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor17">JSON schema validation</a></p></dd>
  <dt><strong>minimum</strong> : <span style="font-family: monospace;">int|float</span></dt>
  <dd><p>The minimum value allowed for a numeric property. This value must be a number.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor21">JSON schema validation</a></p></dd>
  <dt><strong>exclusiveMinimum</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>A boolean value indicating whether the minimum value specified in the minimum attribute is excluded from the set of valid values.<br />
If this attribute is set to true, then the minimum value is not included in the set of valid values.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor21">JSON schema validation</a></p></dd>
  <dt><strong>maxLength</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The maximum length of a string property. A string instance is valid against this property if its length is less than, or equal to, the value of this attribute.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor26">JSON schema validation</a></p></dd>
  <dt><strong>minLength</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The minimum length of a string property. A string instance is valid against this property if its length is greater than, or equal to, the value of this attribute.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor29">JSON schema validation</a></p></dd>
  <dt><strong>maxItems</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The maximum number of items allowed in an array property.<br />
An array instance is valid against this property if its number of items is less than, or equal to, the value of this attribute.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor42">JSON schema validation</a></p></dd>
  <dt><strong>minItems</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The minimum number of items allowed in an array property.<br />
An array instance is valid against this property if its number of items is greater than, or equal to, the value of this attribute.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor45">JSON schema validation</a></p></dd>
  <dt><strong>uniqueItems</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>A boolean value indicating whether all items in an array property must be unique.<br />
If this attribute is set to true, then all items in the array must be unique.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor49">JSON schema validation</a></p></dd>
  <dt><strong>pattern</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A string instance is considered valid if the regular expression matches the instance successfully.</p></dd>
  <dt><strong>enum</strong> : <span style="font-family: monospace;">string[]|int[]|float[]|\UnitEnum[]|class-string</span></dt>
  <dd><p>A collection of allowable values for a property.<br />
A property instance is valid against this attribute if its value is one of the values specified in this collection.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor76">JSON schema validation</a></p></dd>
  <dt><strong>discriminator</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Discriminator|null</span></dt>
  <dd><p>Adds support for polymorphism.<br />
<br />
The discriminator is an object name that is used to differentiate between other schemas which may satisfy the<br />
payload description. See Composition and Inheritance for more details.</p></dd>
  <dt><strong>readOnly</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Declares the property as "read only".<br />
<br />
Relevant only for Schema "properties" definitions.<br />
<br />
This means that it may be sent as part of a response but should not be sent as part of the request.<br />
If the property is marked as readOnly being true and is in the required list, the required will take effect on<br />
the response only. A property must not be marked as both readOnly and writeOnly being true. Default value is<br />
false.</p></dd>
  <dt><strong>writeOnly</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Declares the property as "write only".<br />
<br />
Relevant only for Schema "properties" definitions.<br />
Therefore, it may be sent as part of a request but should not be sent as part of the response.<br />
If the property is marked as writeOnly being true and is in the required list, the required will take effect on<br />
the request only. A property must not be marked as both readOnly and writeOnly being true. Default value is<br />
false.</p></dd>
  <dt><strong>xml</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Xml|null</span></dt>
  <dd><p>This may be used only on properties schemas.<br />
<br />
It has no effect on root schemas.<br />
Adds additional metadata to describe the XML representation of this property.</p></dd>
  <dt><strong>externalDocs</strong> : <span style="font-family: monospace;">OpenApi\Attributes\ExternalDocumentation|null</span></dt>
  <dd><p>Additional external documentation for this schema.</p></dd>
  <dt><strong>example</strong> : <span style="font-family: monospace;">mixed|null</span></dt>
  <dd><p>A free-form property to include an example of an instance for this schema.<br />
<br />
To represent examples that cannot naturally be represented in JSON or YAML, a string value can be used to<br />
contain the example with escaping where necessary.</p></dd>
  <dt><strong>nullable</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Allows sending a null value for the defined schema.<br />
Default value is false.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Specifies that a schema is deprecated and should be transitioned out of usage.<br />
Default value is false.</p></dd>
  <dt><strong>allOf</strong> : <span style="font-family: monospace;">array&lt;Schema|\OpenApi\Annotations\Schema&gt;</span></dt>
  <dd><p>An instance validates successfully against this property if it validates successfully against all schemas<br />
defined by this property's value.</p></dd>
  <dt><strong>anyOf</strong> : <span style="font-family: monospace;">array&lt;Schema|\OpenApi\Annotations\Schema&gt;</span></dt>
  <dd><p>An instance validates successfully against this property if it validates successfully against at least one<br />
schema defined by this property's value.</p></dd>
  <dt><strong>oneOf</strong> : <span style="font-family: monospace;">array&lt;Schema|\OpenApi\Annotations\Schema&gt;</span></dt>
  <dd><p>An instance validates successfully against this property if it validates successfully against exactly one schema<br />
defined by this property's value.</p></dd>
  <dt><strong>additionalProperties</strong> : <span style="font-family: monospace;">OpenApi\Attributes\AdditionalProperties|bool|null</span></dt>
  <dd><p>http://json-schema.org/latest/json-schema-validation.html#anchor64.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [License](https://github.com/zircote/swagger-php/tree/master/src/Attributes/License.php)



#### Allowed in
---
<a href="#info">Info</a>

#### Nested elements
---
<a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>name</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The license name used for the API.</p></dd>
  <dt><strong>identifier</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>An SPDX license expression for the API. The `identifier` field is mutually exclusive of the `url` field.</p></dd>
  <dt><strong>url</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>An URL to the license used for the API. This MUST be in the form of a URL.<br />
<br />
The `url` field is mutually exclusive of the `identifier` field.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [Link](https://github.com/zircote/swagger-php/tree/master/src/Attributes/Link.php)



#### Allowed in
---
<a href="#components">Components</a>, <a href="#response">Response</a>

#### Nested elements
---
<a href="#server">Server</a>, <a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>link</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The key into MediaType->links array.</p></dd>
  <dt><strong>operationRef</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A relative or absolute reference to an OA operation.<br />
<br />
This field is mutually exclusive of the <code>operationId</code> field, and must point to an Operation object.<br />
<br />
Relative values may be used to locate an existing Operation object in the OpenAPI definition.</p></dd>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">string|class-string|object|null</span></dt>
  <dd><p>No details available.</p><p><i>See</i>: <a href="https://swagger.io/docs/specification/using-ref/">Using refs</a></p></dd>
  <dt><strong>operationId</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The name of an existing, resolvable OA operation, as defined with a unique <code>operationId</code>.<br />
<br />
This field is mutually exclusive of the <code>operationRef</code> field.</p></dd>
  <dt><strong>parameters</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;</span></dt>
  <dd><p>A map representing parameters to pass to an operation as specified with operationId or identified via<br />
operationRef.<br />
<br />
The key is the parameter name to be used, whereas the value can be a constant or an expression to<br />
be evaluated and passed to the linked operation.<br />
The parameter name can be qualified using the parameter location [{in}.]{name} for operations<br />
that use the same parameter name in different locations (e.g. path.id).</p></dd>
  <dt><strong>requestBody</strong> : <span style="font-family: monospace;">mixed|null</span></dt>
  <dd><p>A literal value or {expression} to use as a request body when calling the target operation.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A description of the link.<br />
<br />
CommonMark syntax may be used for rich text representation.</p></dd>
  <dt><strong>server</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Server|null</span></dt>
  <dd><p>A server object to be used by the target operation.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [MediaType](https://github.com/zircote/swagger-php/tree/master/src/Attributes/MediaType.php)



#### Allowed in
---
<a href="#response">Response</a>, <a href="#requestbody">RequestBody</a>

#### Nested elements
---
<a href="#schema">Schema</a>, <a href="#examples">Examples</a>, <a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>mediaType</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The key into Operation->content array.</p></dd>
  <dt><strong>schema</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Schema|null</span></dt>
  <dd><p>The schema defining the type used for the request body.</p></dd>
  <dt><strong>example</strong> : <span style="font-family: monospace;">mixed|null</span></dt>
  <dd><p>Example of the media type.<br />
<br />
The example object should be in the correct format as specified by the media type.<br />
The example object is mutually exclusive of the examples object.<br />
<br />
Furthermore, if referencing a schema which contains an example,<br />
the example value shall override the example provided by the schema.</p></dd>
  <dt><strong>examples</strong> : <span style="font-family: monospace;">array&lt;string,Examples&gt;</span></dt>
  <dd><p>Examples of the media type.<br />
<br />
Each example object should match the media type and specified schema if present.<br />
The examples object is mutually exclusive of the example object.<br />
<br />
Furthermore, if referencing a schema which contains an example,<br />
the examples value shall override the example provided by the schema.</p></dd>
  <dt><strong>encoding</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;</span></dt>
  <dd><p>A map between a property name and its encoding information.<br />
<br />
The key, being the property name, must exist in the schema as a property.<br />
<br />
The encoding object shall only apply to requestBody objects when the media type is multipart or<br />
application/x-www-form-urlencoded.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [OpenApi](https://github.com/zircote/swagger-php/tree/master/src/Attributes/OpenApi.php)



#### Nested elements
---
<a href="#info">Info</a>, <a href="#server">Server</a>, <a href="#pathitem">PathItem</a>, <a href="#components">Components</a>, <a href="#tag">Tag</a>, <a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>openapi</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The semantic version number of the OpenAPI Specification version that the OpenAPI document uses.<br />
<br />
The openapi field should be used by tooling specifications and clients to interpret the OpenAPI document.<br />
<br />
A version specified via `Generator::setVersion()` will overwrite this value.<br />
<br />
This is not related to the API info::version string.</p></dd>
  <dt><strong>info</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Info|null</span></dt>
  <dd><p>Provides metadata about the API. The metadata may be used by tooling as required.</p></dd>
  <dt><strong>servers</strong> : <span style="font-family: monospace;">Server[]|null</span></dt>
  <dd><p>An array of <code>@Server</code> objects, which provide connectivity information to a target server.<br />
<br />
If not provided, or is an empty array, the default value would be a Server Object with an url value of <code>/</code>.</p></dd>
  <dt><strong>security</strong> : <span style="font-family: monospace;">array|null</span></dt>
  <dd><p>A declaration of which security mechanisms can be used across the API.<br />
<br />
The list of values includes alternative security requirement objects that can be used.<br />
Only one of the security requirement objects need to be satisfied to authorize a request.<br />
Individual operations can override this definition.<br />
To make security optional, an empty security requirement `({})` can be included in the array.</p></dd>
  <dt><strong>tags</strong> : <span style="font-family: monospace;">Tag[]|null</span></dt>
  <dd><p>A list of tags used by the specification with additional metadata.<br />
<br />
The order of the tags can be used to reflect on their order by the parsing tools.<br />
Not all tags that are used by the Operation Object must be declared.<br />
The tags that are not declared may be organized randomly or based on the tools' logic.<br />
Each tag name in the list must be unique.</p></dd>
  <dt><strong>externalDocs</strong> : <span style="font-family: monospace;">OpenApi\Attributes\ExternalDocumentation|null</span></dt>
  <dd><p>Additional external documentation.</p></dd>
  <dt><strong>paths</strong> : <span style="font-family: monospace;">PathItem[]|null</span></dt>
  <dd><p>The available paths and operations for the API.</p></dd>
  <dt><strong>components</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Components|null</span></dt>
  <dd><p>An element to hold various components for the specification.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [Options](https://github.com/zircote/swagger-php/tree/master/src/Attributes/Options.php)



#### Allowed in
---
<a href="#pathitem">PathItem</a>

#### Nested elements
---
<a href="#parameter">Parameter</a>, <a href="#pathparameter">PathParameter</a>, <a href="#response">Response</a>, <a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#server">Server</a>, <a href="#requestbody">RequestBody</a>, <a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>path</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Key in the OpenApi "Paths Object" for this operation.</p></dd>
  <dt><strong>operationId</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Unique string used to identify the operation.<br />
<br />
The id must be unique among all operations described in the API.<br />
Tools and libraries may use the operationId to uniquely identify an operation, therefore, it is recommended to<br />
follow common programming naming conventions.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A verbose explanation of the operation behavior.<br />
<br />
CommonMark syntax MAY be used for rich text representation.</p></dd>
  <dt><strong>summary</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A short summary of what the operation does.</p></dd>
  <dt><strong>security</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>A declaration of which security mechanisms can be used for this operation.<br />
<br />
The list of values includes alternative security requirement objects that can be used.<br />
<br />
Only one of the security requirement objects need to be satisfied to authorize a request.<br />
<br />
This definition overrides any declared top-level security.<br />
To remove a top-level security declaration, an empty array can be used.</p></dd>
  <dt><strong>servers</strong> : <span style="font-family: monospace;">Server[]</span></dt>
  <dd><p>An alternative server array to service this operation.<br />
<br />
If an alternative server object is specified at the Path Item Object or Root level, it will be overridden by<br />
this value.</p></dd>
  <dt><strong>requestBody</strong> : <span style="font-family: monospace;">OpenApi\Attributes\RequestBody|null</span></dt>
  <dd><p>The request body applicable for this operation.<br />
<br />
The requestBody is only supported in HTTP methods where the HTTP 1.1 specification RFC7231 has explicitly<br />
defined semantics for request bodies. In other cases where the HTTP spec is vague, requestBody shall be ignored<br />
by consumers.</p></dd>
  <dt><strong>tags</strong> : <span style="font-family: monospace;">string[]</span></dt>
  <dd><p>A list of tags for API documentation control.<br />
<br />
Tags can be used for logical grouping of operations by resources or any other qualifier.</p></dd>
  <dt><strong>parameters</strong> : <span style="font-family: monospace;">Parameter[]</span></dt>
  <dd><p>A list of parameters that are applicable for this operation.<br />
<br />
If a parameter is already defined at the Path Item, the new definition will override it but can never remove it.<br />
The list must not include duplicated parameters.<br />
<br />
A unique parameter is defined by a combination of a name and location.<br />
<br />
The list can use the Reference Object to link to parameters that are defined at the OpenAPI Object's<br />
components/parameters.</p></dd>
  <dt><strong>responses</strong> : <span style="font-family: monospace;">Response[]</span></dt>
  <dd><p>The list of possible responses as they are returned from executing this operation.</p></dd>
  <dt><strong>callbacks</strong> : <span style="font-family: monospace;">array|null</span></dt>
  <dd><p>A map of possible out-of band callbacks related to the parent operation.<br />
<br />
The key is a unique identifier for the Callback Object.<br />
<br />
Each value in the map is a Callback Object that describes a request that may be initiated by the API provider<br />
and the expected responses. The key value used to identify the callback object is an expression, evaluated at<br />
runtime, that identifies a URL to use for the callback operation.</p></dd>
  <dt><strong>externalDocs</strong> : <span style="font-family: monospace;">OpenApi\Attributes\ExternalDocumentation|null</span></dt>
  <dd><p>Additional external documentation for this operation.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Declares this operation to be deprecated.<br />
<br />
Consumers should refrain from usage of the declared operation.<br />
<br />
Default value is false.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [Parameter](https://github.com/zircote/swagger-php/tree/master/src/Attributes/Parameter.php)



#### Allowed in
---
<a href="#components">Components</a>, <a href="#pathitem">PathItem</a>, <a href="#operation">Operation</a>, <a href="#get">Get</a>, <a href="#post">Post</a>, <a href="#put">Put</a>, <a href="#delete">Delete</a>, <a href="#patch">Patch</a>, <a href="#head">Head</a>, <a href="#options">Options</a>, <a href="#trace">Trace</a>

#### Nested elements
---
<a href="#schema">Schema</a>, <a href="#examples">Examples</a>, <a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>parameter</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The key into <code>Components::parameters</code> or <code>PathItem::parameters</code> array.</p></dd>
  <dt><strong>name</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The (case-sensitive) name of the parameter.<br />
<br />
If in is "path", the name field must correspond to the associated path segment from the path field in the Paths Object.<br />
<br />
If in is "header" and the name field is "Accept", "Content-Type" or "Authorization", the parameter definition shall be ignored.<br />
For all other cases, the name corresponds to the parameter name used by the in property.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A brief description of the parameter.<br />
<br />
This could contain examples of use.<br />
<br />
CommonMark syntax may be used for rich text representation.</p></dd>
  <dt><strong>in</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The location of the parameter.<br />
<br />
Possible values are "query", "header", "path" or "cookie".</p></dd>
  <dt><strong>required</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Determines whether this parameter is mandatory.<br />
<br />
If the parameter location is "path", this property is required and its value must be true.<br />
Otherwise, the property may be included and its default value is false.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Specifies that a parameter is deprecated and should be transitioned out of usage.</p></dd>
  <dt><strong>allowEmptyValue</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Sets the ability to pass empty-valued parameters.<br />
<br />
This is valid only for query parameters and allows sending a parameter with an empty value.<br />
<br />
Default value is false.<br />
<br />
If style is used, and if behavior is n/a (cannot be serialized), the value of allowEmptyValue shall be ignored.</p></dd>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">string|class-string|object|null</span></dt>
  <dd><p>The relative or absolute path to the endpoint.</p><p><i>See</i>: <a href="https://swagger.io/docs/specification/using-ref/">Using refs</a></p></dd>
  <dt><strong>schema</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Schema|null</span></dt>
  <dd><p>The schema defining the type used for the parameter.</p></dd>
  <dt><strong>example</strong> : <span style="font-family: monospace;">mixed|null</span></dt>
  <dd><p>Example of the media type.<br />
<br />
The example should match the specified schema and encoding properties if present.<br />
The example object is mutually exclusive of the examples object.<br />
Furthermore, if referencing a schema which contains an example, the example value shall override the example provided by the schema.<br />
To represent examples of media types that cannot naturally be represented in JSON or YAML, a string value can contain the example with escaping where necessary.</p></dd>
  <dt><strong>examples</strong> : <span style="font-family: monospace;">array&lt;string,Examples&gt;</span></dt>
  <dd><p>Examples of the media type.<br />
<br />
Each example should contain a value in the correct format as specified in the parameter encoding.<br />
The examples object is mutually exclusive of the example object.<br />
Furthermore, if referencing a schema which contains an example, the examples value shall override the example provided by the schema.</p></dd>
  <dt><strong>content</strong> : <span style="font-family: monospace;">array&lt;MediaType&gt;|JsonContent|XmlContent|Attachable|null</span></dt>
  <dd><p>A map containing the representations for the parameter.<br />
<br />
The key is the media type and the value describes it.<br />
The map must only contain one entry.</p></dd>
  <dt><strong>style</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Describes how the parameter value will be serialized depending on the type of the parameter value.<br />
<br />
Default values (based on value of in): for query - form; for path - simple; for header - simple; for cookie - form.</p></dd>
  <dt><strong>explode</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>When this is true, parameter values of type array or object generate separate parameters for each value of the array or key-value pair of the map.<br />
<br />
For other types of parameters this property has no effect.<br />
<br />
When style is form, the default value is true.<br />
For all other styles, the default value is false.</p></dd>
  <dt><strong>allowReserved</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Determines whether the parameter value should allow reserved characters, as defined by RFC3986 :/?#[]@!$&'()*+,;= to be included without percent-encoding.<br />
<br />
This property only applies to parameters with an in value of query.<br />
<br />
The default value is false.</p></dd>
  <dt><strong>spaceDelimited</strong> : <span style="font-family: monospace;">array|null</span></dt>
  <dd><p>Space separated array values.<br />
<br />
This option replaces collectionFormat equal to ssv from OpenAPI 2.0.</p></dd>
  <dt><strong>pipeDelimited</strong> : <span style="font-family: monospace;">array|null</span></dt>
  <dd><p>Pipe separated array values.<br />
<br />
This option replaces collectionFormat equal to pipes from OpenAPI 2.0.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [Patch](https://github.com/zircote/swagger-php/tree/master/src/Attributes/Patch.php)



#### Allowed in
---
<a href="#pathitem">PathItem</a>

#### Nested elements
---
<a href="#parameter">Parameter</a>, <a href="#pathparameter">PathParameter</a>, <a href="#response">Response</a>, <a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#server">Server</a>, <a href="#requestbody">RequestBody</a>, <a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>path</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Key in the OpenApi "Paths Object" for this operation.</p></dd>
  <dt><strong>operationId</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Unique string used to identify the operation.<br />
<br />
The id must be unique among all operations described in the API.<br />
Tools and libraries may use the operationId to uniquely identify an operation, therefore, it is recommended to<br />
follow common programming naming conventions.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A verbose explanation of the operation behavior.<br />
<br />
CommonMark syntax MAY be used for rich text representation.</p></dd>
  <dt><strong>summary</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A short summary of what the operation does.</p></dd>
  <dt><strong>security</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>A declaration of which security mechanisms can be used for this operation.<br />
<br />
The list of values includes alternative security requirement objects that can be used.<br />
<br />
Only one of the security requirement objects need to be satisfied to authorize a request.<br />
<br />
This definition overrides any declared top-level security.<br />
To remove a top-level security declaration, an empty array can be used.</p></dd>
  <dt><strong>servers</strong> : <span style="font-family: monospace;">Server[]</span></dt>
  <dd><p>An alternative server array to service this operation.<br />
<br />
If an alternative server object is specified at the Path Item Object or Root level, it will be overridden by<br />
this value.</p></dd>
  <dt><strong>requestBody</strong> : <span style="font-family: monospace;">OpenApi\Attributes\RequestBody|null</span></dt>
  <dd><p>The request body applicable for this operation.<br />
<br />
The requestBody is only supported in HTTP methods where the HTTP 1.1 specification RFC7231 has explicitly<br />
defined semantics for request bodies. In other cases where the HTTP spec is vague, requestBody shall be ignored<br />
by consumers.</p></dd>
  <dt><strong>tags</strong> : <span style="font-family: monospace;">string[]</span></dt>
  <dd><p>A list of tags for API documentation control.<br />
<br />
Tags can be used for logical grouping of operations by resources or any other qualifier.</p></dd>
  <dt><strong>parameters</strong> : <span style="font-family: monospace;">Parameter[]</span></dt>
  <dd><p>A list of parameters that are applicable for this operation.<br />
<br />
If a parameter is already defined at the Path Item, the new definition will override it but can never remove it.<br />
The list must not include duplicated parameters.<br />
<br />
A unique parameter is defined by a combination of a name and location.<br />
<br />
The list can use the Reference Object to link to parameters that are defined at the OpenAPI Object's<br />
components/parameters.</p></dd>
  <dt><strong>responses</strong> : <span style="font-family: monospace;">Response[]</span></dt>
  <dd><p>The list of possible responses as they are returned from executing this operation.</p></dd>
  <dt><strong>callbacks</strong> : <span style="font-family: monospace;">array|null</span></dt>
  <dd><p>A map of possible out-of band callbacks related to the parent operation.<br />
<br />
The key is a unique identifier for the Callback Object.<br />
<br />
Each value in the map is a Callback Object that describes a request that may be initiated by the API provider<br />
and the expected responses. The key value used to identify the callback object is an expression, evaluated at<br />
runtime, that identifies a URL to use for the callback operation.</p></dd>
  <dt><strong>externalDocs</strong> : <span style="font-family: monospace;">OpenApi\Attributes\ExternalDocumentation|null</span></dt>
  <dd><p>Additional external documentation for this operation.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Declares this operation to be deprecated.<br />
<br />
Consumers should refrain from usage of the declared operation.<br />
<br />
Default value is false.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [PathItem](https://github.com/zircote/swagger-php/tree/master/src/Attributes/PathItem.php)



#### Allowed in
---
<a href="#openapi">OpenApi</a>

#### Nested elements
---
<a href="#get">Get</a>, <a href="#post">Post</a>, <a href="#put">Put</a>, <a href="#delete">Delete</a>, <a href="#patch">Patch</a>, <a href="#trace">Trace</a>, <a href="#head">Head</a>, <a href="#options">Options</a>, <a href="#parameter">Parameter</a>, <a href="#pathparameter">PathParameter</a>, <a href="#server">Server</a>, <a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>path</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Key for the Path Object (OpenApi->paths array).</p></dd>
  <dt><strong>summary</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>An optional, string summary, intended to apply to all operations in this path.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>An optional, string description, intended to apply to all operations in this path.</p></dd>
  <dt><strong>servers</strong> : <span style="font-family: monospace;">Server[]|null</span></dt>
  <dd><p>An alternative server array to service all operations in this path.</p></dd>
  <dt><strong>parameters</strong> : <span style="font-family: monospace;">Parameter[]|null</span></dt>
  <dd><p>A list of parameters that are applicable for all the operations described under this path.<br />
<br />
These parameters can be overridden at the operation level, but cannot be removed there.<br />
The list must not include duplicated parameters.<br />
A unique parameter is defined by a combination of a name and location.<br />
The list can use the Reference Object to link to parameters that are defined at the OpenAPI Object's components/parameters.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [PathParameter](https://github.com/zircote/swagger-php/tree/master/src/Attributes/PathParameter.php)



#### Allowed in
---
<a href="#components">Components</a>, <a href="#pathitem">PathItem</a>, <a href="#operation">Operation</a>, <a href="#get">Get</a>, <a href="#post">Post</a>, <a href="#put">Put</a>, <a href="#delete">Delete</a>, <a href="#patch">Patch</a>, <a href="#head">Head</a>, <a href="#options">Options</a>, <a href="#trace">Trace</a>

#### Nested elements
---
<a href="#schema">Schema</a>, <a href="#examples">Examples</a>, <a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>parameter</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The key into <code>Components::parameters</code> or <code>PathItem::parameters</code> array.</p></dd>
  <dt><strong>name</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The (case-sensitive) name of the parameter.<br />
<br />
If in is "path", the name field must correspond to the associated path segment from the path field in the Paths Object.<br />
<br />
If in is "header" and the name field is "Accept", "Content-Type" or "Authorization", the parameter definition shall be ignored.<br />
For all other cases, the name corresponds to the parameter name used by the in property.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A brief description of the parameter.<br />
<br />
This could contain examples of use.<br />
<br />
CommonMark syntax may be used for rich text representation.</p></dd>
  <dt><strong>in</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>This takes 'path' as the default location.</p></dd>
  <dt><strong>required</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Specifies that a parameter is deprecated and should be transitioned out of usage.</p></dd>
  <dt><strong>allowEmptyValue</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Sets the ability to pass empty-valued parameters.<br />
<br />
This is valid only for query parameters and allows sending a parameter with an empty value.<br />
<br />
Default value is false.<br />
<br />
If style is used, and if behavior is n/a (cannot be serialized), the value of allowEmptyValue shall be ignored.</p></dd>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">string|class-string|object|null</span></dt>
  <dd><p>The relative or absolute path to the endpoint.</p><p><i>See</i>: <a href="https://swagger.io/docs/specification/using-ref/">Using refs</a></p></dd>
  <dt><strong>schema</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Schema|null</span></dt>
  <dd><p>The schema defining the type used for the parameter.</p></dd>
  <dt><strong>example</strong> : <span style="font-family: monospace;">mixed|null</span></dt>
  <dd><p>Example of the media type.<br />
<br />
The example should match the specified schema and encoding properties if present.<br />
The example object is mutually exclusive of the examples object.<br />
Furthermore, if referencing a schema which contains an example, the example value shall override the example provided by the schema.<br />
To represent examples of media types that cannot naturally be represented in JSON or YAML, a string value can contain the example with escaping where necessary.</p></dd>
  <dt><strong>examples</strong> : <span style="font-family: monospace;">array&lt;string,Examples&gt;</span></dt>
  <dd><p>Examples of the media type.<br />
<br />
Each example should contain a value in the correct format as specified in the parameter encoding.<br />
The examples object is mutually exclusive of the example object.<br />
Furthermore, if referencing a schema which contains an example, the examples value shall override the example provided by the schema.</p></dd>
  <dt><strong>content</strong> : <span style="font-family: monospace;">array&lt;MediaType&gt;|JsonContent|XmlContent|Attachable|null</span></dt>
  <dd><p>A map containing the representations for the parameter.<br />
<br />
The key is the media type and the value describes it.<br />
The map must only contain one entry.</p></dd>
  <dt><strong>style</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Describes how the parameter value will be serialized depending on the type of the parameter value.<br />
<br />
Default values (based on value of in): for query - form; for path - simple; for header - simple; for cookie - form.</p></dd>
  <dt><strong>explode</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>When this is true, parameter values of type array or object generate separate parameters for each value of the array or key-value pair of the map.<br />
<br />
For other types of parameters this property has no effect.<br />
<br />
When style is form, the default value is true.<br />
For all other styles, the default value is false.</p></dd>
  <dt><strong>allowReserved</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Determines whether the parameter value should allow reserved characters, as defined by RFC3986 :/?#[]@!$&'()*+,;= to be included without percent-encoding.<br />
<br />
This property only applies to parameters with an in value of query.<br />
<br />
The default value is false.</p></dd>
  <dt><strong>spaceDelimited</strong> : <span style="font-family: monospace;">array|null</span></dt>
  <dd><p>Space separated array values.<br />
<br />
This option replaces collectionFormat equal to ssv from OpenAPI 2.0.</p></dd>
  <dt><strong>pipeDelimited</strong> : <span style="font-family: monospace;">array|null</span></dt>
  <dd><p>Pipe separated array values.<br />
<br />
This option replaces collectionFormat equal to pipes from OpenAPI 2.0.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [Post](https://github.com/zircote/swagger-php/tree/master/src/Attributes/Post.php)



#### Allowed in
---
<a href="#pathitem">PathItem</a>

#### Nested elements
---
<a href="#parameter">Parameter</a>, <a href="#pathparameter">PathParameter</a>, <a href="#response">Response</a>, <a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#server">Server</a>, <a href="#requestbody">RequestBody</a>, <a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>path</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Key in the OpenApi "Paths Object" for this operation.</p></dd>
  <dt><strong>operationId</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Unique string used to identify the operation.<br />
<br />
The id must be unique among all operations described in the API.<br />
Tools and libraries may use the operationId to uniquely identify an operation, therefore, it is recommended to<br />
follow common programming naming conventions.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A verbose explanation of the operation behavior.<br />
<br />
CommonMark syntax MAY be used for rich text representation.</p></dd>
  <dt><strong>summary</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A short summary of what the operation does.</p></dd>
  <dt><strong>security</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>A declaration of which security mechanisms can be used for this operation.<br />
<br />
The list of values includes alternative security requirement objects that can be used.<br />
<br />
Only one of the security requirement objects need to be satisfied to authorize a request.<br />
<br />
This definition overrides any declared top-level security.<br />
To remove a top-level security declaration, an empty array can be used.</p></dd>
  <dt><strong>servers</strong> : <span style="font-family: monospace;">Server[]</span></dt>
  <dd><p>An alternative server array to service this operation.<br />
<br />
If an alternative server object is specified at the Path Item Object or Root level, it will be overridden by<br />
this value.</p></dd>
  <dt><strong>requestBody</strong> : <span style="font-family: monospace;">OpenApi\Attributes\RequestBody|null</span></dt>
  <dd><p>The request body applicable for this operation.<br />
<br />
The requestBody is only supported in HTTP methods where the HTTP 1.1 specification RFC7231 has explicitly<br />
defined semantics for request bodies. In other cases where the HTTP spec is vague, requestBody shall be ignored<br />
by consumers.</p></dd>
  <dt><strong>tags</strong> : <span style="font-family: monospace;">string[]</span></dt>
  <dd><p>A list of tags for API documentation control.<br />
<br />
Tags can be used for logical grouping of operations by resources or any other qualifier.</p></dd>
  <dt><strong>parameters</strong> : <span style="font-family: monospace;">Parameter[]</span></dt>
  <dd><p>A list of parameters that are applicable for this operation.<br />
<br />
If a parameter is already defined at the Path Item, the new definition will override it but can never remove it.<br />
The list must not include duplicated parameters.<br />
<br />
A unique parameter is defined by a combination of a name and location.<br />
<br />
The list can use the Reference Object to link to parameters that are defined at the OpenAPI Object's<br />
components/parameters.</p></dd>
  <dt><strong>responses</strong> : <span style="font-family: monospace;">Response[]</span></dt>
  <dd><p>The list of possible responses as they are returned from executing this operation.</p></dd>
  <dt><strong>callbacks</strong> : <span style="font-family: monospace;">array|null</span></dt>
  <dd><p>A map of possible out-of band callbacks related to the parent operation.<br />
<br />
The key is a unique identifier for the Callback Object.<br />
<br />
Each value in the map is a Callback Object that describes a request that may be initiated by the API provider<br />
and the expected responses. The key value used to identify the callback object is an expression, evaluated at<br />
runtime, that identifies a URL to use for the callback operation.</p></dd>
  <dt><strong>externalDocs</strong> : <span style="font-family: monospace;">OpenApi\Attributes\ExternalDocumentation|null</span></dt>
  <dd><p>Additional external documentation for this operation.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Declares this operation to be deprecated.<br />
<br />
Consumers should refrain from usage of the declared operation.<br />
<br />
Default value is false.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [Property](https://github.com/zircote/swagger-php/tree/master/src/Attributes/Property.php)



#### Allowed in
---
<a href="#additionalproperties">AdditionalProperties</a>, <a href="#schema">Schema</a>, <a href="#jsoncontent">JsonContent</a>, <a href="#xmlcontent">XmlContent</a>, <a href="#property">Property</a>, <a href="#items">Items</a>

#### Nested elements
---
<a href="#discriminator">Discriminator</a>, <a href="#items">Items</a>, <a href="#property">Property</a>, <a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#xml">Xml</a>, <a href="#additionalproperties">AdditionalProperties</a>, <a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>property</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The key into Schema->properties array.</p></dd>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">string|class-string|object|null</span></dt>
  <dd><p>The relative or absolute path to the endpoint.</p><p><i>See</i>: <a href="https://swagger.io/docs/specification/using-ref/">Using refs</a></p></dd>
  <dt><strong>schema</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The key into Components->schemas array.</p></dd>
  <dt><strong>title</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Can be used to decorate a user interface with information about the data produced by this user interface.<br />
<br />
Preferably short; use <code>description</code> for more details.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A description will provide explanation about the purpose of the instance described by this schema.</p></dd>
  <dt><strong>maxProperties</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The maximum number of properties allowed in an object instance.<br />
An object instance is valid against this property if its number of properties is less than, or equal to, the value of this attribute.</p></dd>
  <dt><strong>minProperties</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The minimum number of properties allowed in an object instance.<br />
An object instance is valid against this property if its number of properties is greater than, or equal to, the value of this attribute.</p></dd>
  <dt><strong>required</strong> : <span style="font-family: monospace;">string[]</span></dt>
  <dd><p>An object instance is valid against this property if its property set contains all elements in this property's<br />
array value.</p></dd>
  <dt><strong>properties</strong> : <span style="font-family: monospace;">Property[]</span></dt>
  <dd><p>A collection of properties to define for an object. Each property is represented as an instance of the <a href="#property">Property</a> class.</p></dd>
  <dt><strong>type</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The type of the schema/property. The value MUST be one of "string", "number", "integer", "boolean", "array" or<br />
"object".</p></dd>
  <dt><strong>format</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The extending format for the previously mentioned type. See Data Type Formats for further details.</p></dd>
  <dt><strong>items</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Items|null</span></dt>
  <dd><p>Required if type is "array". Describes the type of items in the array.</p></dd>
  <dt><strong>collectionFormat</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Determines the format of the array if type array is used.<br />
Possible values are:<br />
- csv: comma separated values foo,bar.<br />
- ssv: space separated values foo bar.<br />
- tsv: tab separated values foo\tbar.<br />
- pipes: pipe separated values foo|bar.<br />
- multi: corresponds to multiple parameter instances instead of multiple values for a single instance foo=bar&foo=baz.<br />
This is valid only for parameters of type <code>query</code> or <code>formData</code>.<br />
Default value is csv.</p></dd>
  <dt><strong>default</strong> : <span style="font-family: monospace;">mixed|null</span></dt>
  <dd><p>Sets a default value to the parameter. The type of the value depends on the defined type.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor101">JSON schema validation</a></p></dd>
  <dt><strong>maximum</strong> : <span style="font-family: monospace;">int|float</span></dt>
  <dd><p>The maximum value allowed for a numeric property. This value must be a number.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor17">JSON schema validation</a></p></dd>
  <dt><strong>exclusiveMaximum</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>A boolean value indicating whether the maximum value specified in the maximum attribute is excluded from the set of valid values.<br />
If this attribute is set to true, then the maximum value is not included in the set of valid values.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor17">JSON schema validation</a></p></dd>
  <dt><strong>minimum</strong> : <span style="font-family: monospace;">int|float</span></dt>
  <dd><p>The minimum value allowed for a numeric property. This value must be a number.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor21">JSON schema validation</a></p></dd>
  <dt><strong>exclusiveMinimum</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>A boolean value indicating whether the minimum value specified in the minimum attribute is excluded from the set of valid values.<br />
If this attribute is set to true, then the minimum value is not included in the set of valid values.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor21">JSON schema validation</a></p></dd>
  <dt><strong>maxLength</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The maximum length of a string property. A string instance is valid against this property if its length is less than, or equal to, the value of this attribute.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor26">JSON schema validation</a></p></dd>
  <dt><strong>minLength</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The minimum length of a string property. A string instance is valid against this property if its length is greater than, or equal to, the value of this attribute.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor29">JSON schema validation</a></p></dd>
  <dt><strong>maxItems</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The maximum number of items allowed in an array property.<br />
An array instance is valid against this property if its number of items is less than, or equal to, the value of this attribute.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor42">JSON schema validation</a></p></dd>
  <dt><strong>minItems</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The minimum number of items allowed in an array property.<br />
An array instance is valid against this property if its number of items is greater than, or equal to, the value of this attribute.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor45">JSON schema validation</a></p></dd>
  <dt><strong>uniqueItems</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>A boolean value indicating whether all items in an array property must be unique.<br />
If this attribute is set to true, then all items in the array must be unique.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor49">JSON schema validation</a></p></dd>
  <dt><strong>pattern</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A string instance is considered valid if the regular expression matches the instance successfully.</p></dd>
  <dt><strong>enum</strong> : <span style="font-family: monospace;">string[]|int[]|float[]|\UnitEnum[]|class-string</span></dt>
  <dd><p>A collection of allowable values for a property.<br />
A property instance is valid against this attribute if its value is one of the values specified in this collection.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor76">JSON schema validation</a></p></dd>
  <dt><strong>discriminator</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Discriminator|null</span></dt>
  <dd><p>Adds support for polymorphism.<br />
<br />
The discriminator is an object name that is used to differentiate between other schemas which may satisfy the<br />
payload description. See Composition and Inheritance for more details.</p></dd>
  <dt><strong>readOnly</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Declares the property as "read only".<br />
<br />
Relevant only for Schema "properties" definitions.<br />
<br />
This means that it may be sent as part of a response but should not be sent as part of the request.<br />
If the property is marked as readOnly being true and is in the required list, the required will take effect on<br />
the response only. A property must not be marked as both readOnly and writeOnly being true. Default value is<br />
false.</p></dd>
  <dt><strong>writeOnly</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Declares the property as "write only".<br />
<br />
Relevant only for Schema "properties" definitions.<br />
Therefore, it may be sent as part of a request but should not be sent as part of the response.<br />
If the property is marked as writeOnly being true and is in the required list, the required will take effect on<br />
the request only. A property must not be marked as both readOnly and writeOnly being true. Default value is<br />
false.</p></dd>
  <dt><strong>xml</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Xml|null</span></dt>
  <dd><p>This may be used only on properties schemas.<br />
<br />
It has no effect on root schemas.<br />
Adds additional metadata to describe the XML representation of this property.</p></dd>
  <dt><strong>externalDocs</strong> : <span style="font-family: monospace;">OpenApi\Attributes\ExternalDocumentation|null</span></dt>
  <dd><p>Additional external documentation for this schema.</p></dd>
  <dt><strong>example</strong> : <span style="font-family: monospace;">mixed|null</span></dt>
  <dd><p>A free-form property to include an example of an instance for this schema.<br />
<br />
To represent examples that cannot naturally be represented in JSON or YAML, a string value can be used to<br />
contain the example with escaping where necessary.</p></dd>
  <dt><strong>nullable</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Allows sending a null value for the defined schema.<br />
Default value is false.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Specifies that a schema is deprecated and should be transitioned out of usage.<br />
Default value is false.</p></dd>
  <dt><strong>allOf</strong> : <span style="font-family: monospace;">array&lt;Schema|\OpenApi\Annotations\Schema&gt;</span></dt>
  <dd><p>An instance validates successfully against this property if it validates successfully against all schemas<br />
defined by this property's value.</p></dd>
  <dt><strong>anyOf</strong> : <span style="font-family: monospace;">array&lt;Schema|\OpenApi\Annotations\Schema&gt;</span></dt>
  <dd><p>An instance validates successfully against this property if it validates successfully against at least one<br />
schema defined by this property's value.</p></dd>
  <dt><strong>oneOf</strong> : <span style="font-family: monospace;">array&lt;Schema|\OpenApi\Annotations\Schema&gt;</span></dt>
  <dd><p>An instance validates successfully against this property if it validates successfully against exactly one schema<br />
defined by this property's value.</p></dd>
  <dt><strong>additionalProperties</strong> : <span style="font-family: monospace;">OpenApi\Attributes\AdditionalProperties|bool|null</span></dt>
  <dd><p>http://json-schema.org/latest/json-schema-validation.html#anchor64.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [Put](https://github.com/zircote/swagger-php/tree/master/src/Attributes/Put.php)



#### Allowed in
---
<a href="#pathitem">PathItem</a>

#### Nested elements
---
<a href="#parameter">Parameter</a>, <a href="#pathparameter">PathParameter</a>, <a href="#response">Response</a>, <a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#server">Server</a>, <a href="#requestbody">RequestBody</a>, <a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>path</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Key in the OpenApi "Paths Object" for this operation.</p></dd>
  <dt><strong>operationId</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Unique string used to identify the operation.<br />
<br />
The id must be unique among all operations described in the API.<br />
Tools and libraries may use the operationId to uniquely identify an operation, therefore, it is recommended to<br />
follow common programming naming conventions.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A verbose explanation of the operation behavior.<br />
<br />
CommonMark syntax MAY be used for rich text representation.</p></dd>
  <dt><strong>summary</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A short summary of what the operation does.</p></dd>
  <dt><strong>security</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>A declaration of which security mechanisms can be used for this operation.<br />
<br />
The list of values includes alternative security requirement objects that can be used.<br />
<br />
Only one of the security requirement objects need to be satisfied to authorize a request.<br />
<br />
This definition overrides any declared top-level security.<br />
To remove a top-level security declaration, an empty array can be used.</p></dd>
  <dt><strong>servers</strong> : <span style="font-family: monospace;">Server[]</span></dt>
  <dd><p>An alternative server array to service this operation.<br />
<br />
If an alternative server object is specified at the Path Item Object or Root level, it will be overridden by<br />
this value.</p></dd>
  <dt><strong>requestBody</strong> : <span style="font-family: monospace;">OpenApi\Attributes\RequestBody|null</span></dt>
  <dd><p>The request body applicable for this operation.<br />
<br />
The requestBody is only supported in HTTP methods where the HTTP 1.1 specification RFC7231 has explicitly<br />
defined semantics for request bodies. In other cases where the HTTP spec is vague, requestBody shall be ignored<br />
by consumers.</p></dd>
  <dt><strong>tags</strong> : <span style="font-family: monospace;">string[]</span></dt>
  <dd><p>A list of tags for API documentation control.<br />
<br />
Tags can be used for logical grouping of operations by resources or any other qualifier.</p></dd>
  <dt><strong>parameters</strong> : <span style="font-family: monospace;">Parameter[]</span></dt>
  <dd><p>A list of parameters that are applicable for this operation.<br />
<br />
If a parameter is already defined at the Path Item, the new definition will override it but can never remove it.<br />
The list must not include duplicated parameters.<br />
<br />
A unique parameter is defined by a combination of a name and location.<br />
<br />
The list can use the Reference Object to link to parameters that are defined at the OpenAPI Object's<br />
components/parameters.</p></dd>
  <dt><strong>responses</strong> : <span style="font-family: monospace;">Response[]</span></dt>
  <dd><p>The list of possible responses as they are returned from executing this operation.</p></dd>
  <dt><strong>callbacks</strong> : <span style="font-family: monospace;">array|null</span></dt>
  <dd><p>A map of possible out-of band callbacks related to the parent operation.<br />
<br />
The key is a unique identifier for the Callback Object.<br />
<br />
Each value in the map is a Callback Object that describes a request that may be initiated by the API provider<br />
and the expected responses. The key value used to identify the callback object is an expression, evaluated at<br />
runtime, that identifies a URL to use for the callback operation.</p></dd>
  <dt><strong>externalDocs</strong> : <span style="font-family: monospace;">OpenApi\Attributes\ExternalDocumentation|null</span></dt>
  <dd><p>Additional external documentation for this operation.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Declares this operation to be deprecated.<br />
<br />
Consumers should refrain from usage of the declared operation.<br />
<br />
Default value is false.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [QueryParameter](https://github.com/zircote/swagger-php/tree/master/src/Attributes/QueryParameter.php)



#### Allowed in
---
<a href="#components">Components</a>, <a href="#pathitem">PathItem</a>, <a href="#operation">Operation</a>, <a href="#get">Get</a>, <a href="#post">Post</a>, <a href="#put">Put</a>, <a href="#delete">Delete</a>, <a href="#patch">Patch</a>, <a href="#head">Head</a>, <a href="#options">Options</a>, <a href="#trace">Trace</a>

#### Nested elements
---
<a href="#schema">Schema</a>, <a href="#examples">Examples</a>, <a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>parameter</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The key into <code>Components::parameters</code> or <code>PathItem::parameters</code> array.</p></dd>
  <dt><strong>name</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The (case-sensitive) name of the parameter.<br />
<br />
If in is "path", the name field must correspond to the associated path segment from the path field in the Paths Object.<br />
<br />
If in is "header" and the name field is "Accept", "Content-Type" or "Authorization", the parameter definition shall be ignored.<br />
For all other cases, the name corresponds to the parameter name used by the in property.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A brief description of the parameter.<br />
<br />
This could contain examples of use.<br />
<br />
CommonMark syntax may be used for rich text representation.</p></dd>
  <dt><strong>in</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>This takes 'query' as the default location.</p></dd>
  <dt><strong>required</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Determines whether this parameter is mandatory.<br />
<br />
If the parameter location is "path", this property is required and its value must be true.<br />
Otherwise, the property may be included and its default value is false.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Specifies that a parameter is deprecated and should be transitioned out of usage.</p></dd>
  <dt><strong>allowEmptyValue</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Sets the ability to pass empty-valued parameters.<br />
<br />
This is valid only for query parameters and allows sending a parameter with an empty value.<br />
<br />
Default value is false.<br />
<br />
If style is used, and if behavior is n/a (cannot be serialized), the value of allowEmptyValue shall be ignored.</p></dd>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">string|class-string|object|null</span></dt>
  <dd><p>The relative or absolute path to the endpoint.</p><p><i>See</i>: <a href="https://swagger.io/docs/specification/using-ref/">Using refs</a></p></dd>
  <dt><strong>schema</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Schema|null</span></dt>
  <dd><p>The schema defining the type used for the parameter.</p></dd>
  <dt><strong>example</strong> : <span style="font-family: monospace;">mixed|null</span></dt>
  <dd><p>Example of the media type.<br />
<br />
The example should match the specified schema and encoding properties if present.<br />
The example object is mutually exclusive of the examples object.<br />
Furthermore, if referencing a schema which contains an example, the example value shall override the example provided by the schema.<br />
To represent examples of media types that cannot naturally be represented in JSON or YAML, a string value can contain the example with escaping where necessary.</p></dd>
  <dt><strong>examples</strong> : <span style="font-family: monospace;">array&lt;string,Examples&gt;</span></dt>
  <dd><p>Examples of the media type.<br />
<br />
Each example should contain a value in the correct format as specified in the parameter encoding.<br />
The examples object is mutually exclusive of the example object.<br />
Furthermore, if referencing a schema which contains an example, the examples value shall override the example provided by the schema.</p></dd>
  <dt><strong>content</strong> : <span style="font-family: monospace;">array&lt;MediaType&gt;|JsonContent|XmlContent|Attachable|null</span></dt>
  <dd><p>A map containing the representations for the parameter.<br />
<br />
The key is the media type and the value describes it.<br />
The map must only contain one entry.</p></dd>
  <dt><strong>style</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Describes how the parameter value will be serialized depending on the type of the parameter value.<br />
<br />
Default values (based on value of in): for query - form; for path - simple; for header - simple; for cookie - form.</p></dd>
  <dt><strong>explode</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>When this is true, parameter values of type array or object generate separate parameters for each value of the array or key-value pair of the map.<br />
<br />
For other types of parameters this property has no effect.<br />
<br />
When style is form, the default value is true.<br />
For all other styles, the default value is false.</p></dd>
  <dt><strong>allowReserved</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Determines whether the parameter value should allow reserved characters, as defined by RFC3986 :/?#[]@!$&'()*+,;= to be included without percent-encoding.<br />
<br />
This property only applies to parameters with an in value of query.<br />
<br />
The default value is false.</p></dd>
  <dt><strong>spaceDelimited</strong> : <span style="font-family: monospace;">array|null</span></dt>
  <dd><p>Space separated array values.<br />
<br />
This option replaces collectionFormat equal to ssv from OpenAPI 2.0.</p></dd>
  <dt><strong>pipeDelimited</strong> : <span style="font-family: monospace;">array|null</span></dt>
  <dd><p>Pipe separated array values.<br />
<br />
This option replaces collectionFormat equal to pipes from OpenAPI 2.0.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [RequestBody](https://github.com/zircote/swagger-php/tree/master/src/Attributes/RequestBody.php)



#### Allowed in
---
<a href="#components">Components</a>, <a href="#delete">Delete</a>, <a href="#get">Get</a>, <a href="#head">Head</a>, <a href="#operation">Operation</a>, <a href="#options">Options</a>, <a href="#patch">Patch</a>, <a href="#post">Post</a>, <a href="#trace">Trace</a>, <a href="#put">Put</a>

#### Nested elements
---
<a href="#mediatype">MediaType</a>, <a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">string|class-string|object|null</span></dt>
  <dd><p>The relative or absolute path to a request body.</p><p><i>See</i>: <a href="https://swagger.io/docs/specification/using-ref/">Using refs</a></p></dd>
  <dt><strong>request</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Request body model name.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A brief description of the parameter.<br />
<br />
This could contain examples of use.<br />
<br />
CommonMark syntax may be used for rich text representation.</p></dd>
  <dt><strong>required</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Determines whether this parameter is mandatory.<br />
<br />
If the parameter location is "path", this property is required and its value must be true.<br />
Otherwise, the property may be included and its default value is false.</p></dd>
  <dt><strong>content</strong> : <span style="font-family: monospace;">array&lt;MediaType&gt;|MediaType|JsonContent|XmlContent|Attachable|null</span></dt>
  <dd><p>The content of the request body.<br />
<br />
The key is a media type or media type range and the value describes it. For requests that match multiple keys,<br />
only the most specific key is applicable. e.g. text/plain overrides text/*.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [Response](https://github.com/zircote/swagger-php/tree/master/src/Attributes/Response.php)



#### Allowed in
---
<a href="#components">Components</a>, <a href="#operation">Operation</a>, <a href="#get">Get</a>, <a href="#post">Post</a>, <a href="#put">Put</a>, <a href="#patch">Patch</a>, <a href="#delete">Delete</a>, <a href="#head">Head</a>, <a href="#options">Options</a>, <a href="#trace">Trace</a>

#### Nested elements
---
<a href="#mediatype">MediaType</a>, <a href="#header">Header</a>, <a href="#link">Link</a>, <a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">string|class-string|object|null</span></dt>
  <dd><p>The relative or absolute path to a response.</p><p><i>See</i>: <a href="https://swagger.io/docs/specification/using-ref/">Using refs</a></p></dd>
  <dt><strong>response</strong> : <span style="font-family: monospace;">string|int|null</span></dt>
  <dd><p>The key into Operations->responses array.<br />
<br />
A HTTP status code or <code>default</code>.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A short description of the response.<br />
<br />
CommonMark syntax may be used for rich text representation.</p></dd>
  <dt><strong>headers</strong> : <span style="font-family: monospace;">Header[]</span></dt>
  <dd><p>Maps a header name to its definition.<br />
<br />
RFC7230 states header names are case insensitive.<br />
<br />
If a response header is defined with the name "Content-Type", it shall be ignored.</p><p><i>See</i>: <a href="https://tools.ietf.org/html/rfc7230#page-22">RFC7230</a></p></dd>
  <dt><strong>content</strong> : <span style="font-family: monospace;">MediaType|JsonContent|XmlContent|Attachable|array&lt;MediaType|Attachable&gt;</span></dt>
  <dd><p>A map containing descriptions of potential response payloads.<br />
<br />
The key is a media type or media type range and the value describes it.<br />
<br />
For responses that match multiple keys, only the most specific key is applicable;<br />
e.g. <code>text/plain</code> overrides <code>text/*</code>.</p></dd>
  <dt><strong>links</strong> : <span style="font-family: monospace;">Link[]</span></dt>
  <dd><p>A map of operations links that can be followed from the response.<br />
<br />
The key of the map is a short name for the link, following the naming constraints of the names for Component<br />
Objects.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [Schema](https://github.com/zircote/swagger-php/tree/master/src/Attributes/Schema.php)



#### Allowed in
---
<a href="#components">Components</a>, <a href="#parameter">Parameter</a>, <a href="#pathparameter">PathParameter</a>, <a href="#mediatype">MediaType</a>, <a href="#header">Header</a>

#### Nested elements
---
<a href="#discriminator">Discriminator</a>, <a href="#items">Items</a>, <a href="#property">Property</a>, <a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#xml">Xml</a>, <a href="#additionalproperties">AdditionalProperties</a>, <a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">string|class-string|object|null</span></dt>
  <dd><p>The relative or absolute path to the endpoint.</p><p><i>See</i>: <a href="https://swagger.io/docs/specification/using-ref/">Using refs</a></p></dd>
  <dt><strong>schema</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The key into Components->schemas array.</p></dd>
  <dt><strong>title</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Can be used to decorate a user interface with information about the data produced by this user interface.<br />
<br />
Preferably short; use <code>description</code> for more details.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A description will provide explanation about the purpose of the instance described by this schema.</p></dd>
  <dt><strong>maxProperties</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The maximum number of properties allowed in an object instance.<br />
An object instance is valid against this property if its number of properties is less than, or equal to, the value of this attribute.</p></dd>
  <dt><strong>minProperties</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The minimum number of properties allowed in an object instance.<br />
An object instance is valid against this property if its number of properties is greater than, or equal to, the value of this attribute.</p></dd>
  <dt><strong>required</strong> : <span style="font-family: monospace;">string[]</span></dt>
  <dd><p>An object instance is valid against this property if its property set contains all elements in this property's<br />
array value.</p></dd>
  <dt><strong>properties</strong> : <span style="font-family: monospace;">Property[]</span></dt>
  <dd><p>A collection of properties to define for an object. Each property is represented as an instance of the <a href="#property">Property</a> class.</p></dd>
  <dt><strong>type</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The type of the schema/property. The value MUST be one of "string", "number", "integer", "boolean", "array" or<br />
"object".</p></dd>
  <dt><strong>format</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The extending format for the previously mentioned type. See Data Type Formats for further details.</p></dd>
  <dt><strong>items</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Items|null</span></dt>
  <dd><p>Required if type is "array". Describes the type of items in the array.</p></dd>
  <dt><strong>collectionFormat</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Determines the format of the array if type array is used.<br />
Possible values are:<br />
- csv: comma separated values foo,bar.<br />
- ssv: space separated values foo bar.<br />
- tsv: tab separated values foo\tbar.<br />
- pipes: pipe separated values foo|bar.<br />
- multi: corresponds to multiple parameter instances instead of multiple values for a single instance foo=bar&foo=baz.<br />
This is valid only for parameters of type <code>query</code> or <code>formData</code>.<br />
Default value is csv.</p></dd>
  <dt><strong>default</strong> : <span style="font-family: monospace;">mixed|null</span></dt>
  <dd><p>Sets a default value to the parameter. The type of the value depends on the defined type.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor101">JSON schema validation</a></p></dd>
  <dt><strong>maximum</strong> : <span style="font-family: monospace;">int|float</span></dt>
  <dd><p>The maximum value allowed for a numeric property. This value must be a number.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor17">JSON schema validation</a></p></dd>
  <dt><strong>exclusiveMaximum</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>A boolean value indicating whether the maximum value specified in the maximum attribute is excluded from the set of valid values.<br />
If this attribute is set to true, then the maximum value is not included in the set of valid values.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor17">JSON schema validation</a></p></dd>
  <dt><strong>minimum</strong> : <span style="font-family: monospace;">int|float</span></dt>
  <dd><p>The minimum value allowed for a numeric property. This value must be a number.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor21">JSON schema validation</a></p></dd>
  <dt><strong>exclusiveMinimum</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>A boolean value indicating whether the minimum value specified in the minimum attribute is excluded from the set of valid values.<br />
If this attribute is set to true, then the minimum value is not included in the set of valid values.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor21">JSON schema validation</a></p></dd>
  <dt><strong>maxLength</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The maximum length of a string property. A string instance is valid against this property if its length is less than, or equal to, the value of this attribute.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor26">JSON schema validation</a></p></dd>
  <dt><strong>minLength</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The minimum length of a string property. A string instance is valid against this property if its length is greater than, or equal to, the value of this attribute.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor29">JSON schema validation</a></p></dd>
  <dt><strong>maxItems</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The maximum number of items allowed in an array property.<br />
An array instance is valid against this property if its number of items is less than, or equal to, the value of this attribute.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor42">JSON schema validation</a></p></dd>
  <dt><strong>minItems</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The minimum number of items allowed in an array property.<br />
An array instance is valid against this property if its number of items is greater than, or equal to, the value of this attribute.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor45">JSON schema validation</a></p></dd>
  <dt><strong>uniqueItems</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>A boolean value indicating whether all items in an array property must be unique.<br />
If this attribute is set to true, then all items in the array must be unique.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor49">JSON schema validation</a></p></dd>
  <dt><strong>pattern</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A string instance is considered valid if the regular expression matches the instance successfully.</p></dd>
  <dt><strong>enum</strong> : <span style="font-family: monospace;">string[]|int[]|float[]|\UnitEnum[]|class-string</span></dt>
  <dd><p>A collection of allowable values for a property.<br />
A property instance is valid against this attribute if its value is one of the values specified in this collection.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor76">JSON schema validation</a></p></dd>
  <dt><strong>discriminator</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Discriminator|null</span></dt>
  <dd><p>Adds support for polymorphism.<br />
<br />
The discriminator is an object name that is used to differentiate between other schemas which may satisfy the<br />
payload description. See Composition and Inheritance for more details.</p></dd>
  <dt><strong>readOnly</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Declares the property as "read only".<br />
<br />
Relevant only for Schema "properties" definitions.<br />
<br />
This means that it may be sent as part of a response but should not be sent as part of the request.<br />
If the property is marked as readOnly being true and is in the required list, the required will take effect on<br />
the response only. A property must not be marked as both readOnly and writeOnly being true. Default value is<br />
false.</p></dd>
  <dt><strong>writeOnly</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Declares the property as "write only".<br />
<br />
Relevant only for Schema "properties" definitions.<br />
Therefore, it may be sent as part of a request but should not be sent as part of the response.<br />
If the property is marked as writeOnly being true and is in the required list, the required will take effect on<br />
the request only. A property must not be marked as both readOnly and writeOnly being true. Default value is<br />
false.</p></dd>
  <dt><strong>xml</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Xml|null</span></dt>
  <dd><p>This may be used only on properties schemas.<br />
<br />
It has no effect on root schemas.<br />
Adds additional metadata to describe the XML representation of this property.</p></dd>
  <dt><strong>externalDocs</strong> : <span style="font-family: monospace;">OpenApi\Attributes\ExternalDocumentation|null</span></dt>
  <dd><p>Additional external documentation for this schema.</p></dd>
  <dt><strong>example</strong> : <span style="font-family: monospace;">mixed|null</span></dt>
  <dd><p>A free-form property to include an example of an instance for this schema.<br />
<br />
To represent examples that cannot naturally be represented in JSON or YAML, a string value can be used to<br />
contain the example with escaping where necessary.</p></dd>
  <dt><strong>nullable</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Allows sending a null value for the defined schema.<br />
Default value is false.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Specifies that a schema is deprecated and should be transitioned out of usage.<br />
Default value is false.</p></dd>
  <dt><strong>allOf</strong> : <span style="font-family: monospace;">array&lt;Schema|\OpenApi\Annotations\Schema&gt;</span></dt>
  <dd><p>An instance validates successfully against this property if it validates successfully against all schemas<br />
defined by this property's value.</p></dd>
  <dt><strong>anyOf</strong> : <span style="font-family: monospace;">array&lt;Schema|\OpenApi\Annotations\Schema&gt;</span></dt>
  <dd><p>An instance validates successfully against this property if it validates successfully against at least one<br />
schema defined by this property's value.</p></dd>
  <dt><strong>oneOf</strong> : <span style="font-family: monospace;">array&lt;Schema|\OpenApi\Annotations\Schema&gt;</span></dt>
  <dd><p>An instance validates successfully against this property if it validates successfully against exactly one schema<br />
defined by this property's value.</p></dd>
  <dt><strong>additionalProperties</strong> : <span style="font-family: monospace;">OpenApi\Attributes\AdditionalProperties|bool|null</span></dt>
  <dd><p>http://json-schema.org/latest/json-schema-validation.html#anchor64.</p></dd>
  <dt><strong>const</strong> : <span style="font-family: monospace;">mixed</span></dt>
  <dd><p>http://json-schema.org/draft/2020-12/json-schema-validation.html#rfc.section.6.1.3.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [SecurityScheme](https://github.com/zircote/swagger-php/tree/master/src/Attributes/SecurityScheme.php)



#### Allowed in
---
<a href="#components">Components</a>

#### Nested elements
---
<a href="#flow">Flow</a>, <a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">string|class-string|object|null</span></dt>
  <dd><p>The relative or absolute path to a security scheme.</p><p><i>See</i>: <a href="https://swagger.io/docs/specification/using-ref/">Using refs</a></p></dd>
  <dt><strong>securityScheme</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The key into OpenApi->security array.</p></dd>
  <dt><strong>type</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The type of the security scheme.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A short description for security scheme.</p></dd>
  <dt><strong>name</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The name of the header or query parameter to be used.</p></dd>
  <dt><strong>in</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Required The location of the API key.</p></dd>
  <dt><strong>bearerFormat</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A hint to the client to identify how the bearer token is formatted.<br />
<br />
Bearer tokens are usually generated by an authorization server, so this information is primarily for documentation purposes.</p></dd>
  <dt><strong>scheme</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The name of the HTTP Authorization scheme.</p><p><i>See</i>: <a href="https://tools.ietf.org/html/rfc7235#section-5.1">RFC7235</a></p></dd>
  <dt><strong>openIdConnectUrl</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>OpenId Connect URL to discover OAuth2 configuration values. This MUST be in the form of a URL.</p></dd>
  <dt><strong>flows</strong> : <span style="font-family: monospace;">Flow[]</span></dt>
  <dd><p>The flow used by the OAuth2 security scheme.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [Server](https://github.com/zircote/swagger-php/tree/master/src/Attributes/Server.php)



#### Allowed in
---
<a href="#openapi">OpenApi</a>, <a href="#pathitem">PathItem</a>, <a href="#operation">Operation</a>, <a href="#get">Get</a>, <a href="#post">Post</a>, <a href="#put">Put</a>, <a href="#delete">Delete</a>, <a href="#patch">Patch</a>, <a href="#head">Head</a>, <a href="#options">Options</a>, <a href="#trace">Trace</a>, <a href="#link">Link</a>

#### Nested elements
---
<a href="#servervariable">ServerVariable</a>, <a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>url</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>An URL to the target host.<br />
<br />
This URL supports Server Variables and may be relative,<br />
to indicate that the host location is relative to the location where the OpenAPI document is being served.<br />
Variable substitutions will be made when a variable is named in {brackets}.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>An optional string describing the host designated by the URL.<br />
<br />
CommonMark syntax may be used for rich text representation.</p></dd>
  <dt><strong>variables</strong> : <span style="font-family: monospace;">ServerVariable[]</span></dt>
  <dd><p>A map between a variable name and its value.<br />
<br />
The value is used for substitution in the server's URL template.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [ServerVariable](https://github.com/zircote/swagger-php/tree/master/src/Attributes/ServerVariable.php)



#### Allowed in
---
<a href="#server">Server</a>

#### Nested elements
---
<a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>serverVariable</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The key into Server->variables array.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>An optional description for the server variable.<br />
<br />
CommonMark syntax MAY be used for rich text representation.</p></dd>
  <dt><strong>default</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The default value to use for substitution, and to send, if an alternate value is not supplied.<br />
<br />
Unlike the Schema Object's default, this value must be provided by the consumer.</p></dd>
  <dt><strong>enum</strong> : <span style="font-family: monospace;">string[]|int[]|float[]|\UnitEnum[]|class-string|null</span></dt>
  <dd><p>An enumeration of values to be used if the substitution options are from a limited set.</p></dd>
  <dt><strong>variables</strong> : <span style="font-family: monospace;">array|null</span></dt>
  <dd><p>A map between a variable name and its value.<br />
<br />
The value is used for substitution in the server's URL template.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [Tag](https://github.com/zircote/swagger-php/tree/master/src/Attributes/Tag.php)



#### Allowed in
---
<a href="#openapi">OpenApi</a>

#### Nested elements
---
<a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>name</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The name of the tag.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A short description for the tag. GFM syntax can be used for rich text representation.</p></dd>
  <dt><strong>externalDocs</strong> : <span style="font-family: monospace;">OpenApi\Attributes\ExternalDocumentation|null</span></dt>
  <dd><p>Additional external documentation for this tag.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [Trace](https://github.com/zircote/swagger-php/tree/master/src/Attributes/Trace.php)



#### Allowed in
---
<a href="#pathitem">PathItem</a>

#### Nested elements
---
<a href="#parameter">Parameter</a>, <a href="#pathparameter">PathParameter</a>, <a href="#response">Response</a>, <a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#server">Server</a>, <a href="#requestbody">RequestBody</a>, <a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>path</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Key in the OpenApi "Paths Object" for this operation.</p></dd>
  <dt><strong>operationId</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Unique string used to identify the operation.<br />
<br />
The id must be unique among all operations described in the API.<br />
Tools and libraries may use the operationId to uniquely identify an operation, therefore, it is recommended to<br />
follow common programming naming conventions.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A verbose explanation of the operation behavior.<br />
<br />
CommonMark syntax MAY be used for rich text representation.</p></dd>
  <dt><strong>summary</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A short summary of what the operation does.</p></dd>
  <dt><strong>security</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>A declaration of which security mechanisms can be used for this operation.<br />
<br />
The list of values includes alternative security requirement objects that can be used.<br />
<br />
Only one of the security requirement objects need to be satisfied to authorize a request.<br />
<br />
This definition overrides any declared top-level security.<br />
To remove a top-level security declaration, an empty array can be used.</p></dd>
  <dt><strong>servers</strong> : <span style="font-family: monospace;">Server[]</span></dt>
  <dd><p>An alternative server array to service this operation.<br />
<br />
If an alternative server object is specified at the Path Item Object or Root level, it will be overridden by<br />
this value.</p></dd>
  <dt><strong>requestBody</strong> : <span style="font-family: monospace;">OpenApi\Attributes\RequestBody|null</span></dt>
  <dd><p>The request body applicable for this operation.<br />
<br />
The requestBody is only supported in HTTP methods where the HTTP 1.1 specification RFC7231 has explicitly<br />
defined semantics for request bodies. In other cases where the HTTP spec is vague, requestBody shall be ignored<br />
by consumers.</p></dd>
  <dt><strong>tags</strong> : <span style="font-family: monospace;">string[]</span></dt>
  <dd><p>A list of tags for API documentation control.<br />
<br />
Tags can be used for logical grouping of operations by resources or any other qualifier.</p></dd>
  <dt><strong>parameters</strong> : <span style="font-family: monospace;">Parameter[]</span></dt>
  <dd><p>A list of parameters that are applicable for this operation.<br />
<br />
If a parameter is already defined at the Path Item, the new definition will override it but can never remove it.<br />
The list must not include duplicated parameters.<br />
<br />
A unique parameter is defined by a combination of a name and location.<br />
<br />
The list can use the Reference Object to link to parameters that are defined at the OpenAPI Object's<br />
components/parameters.</p></dd>
  <dt><strong>responses</strong> : <span style="font-family: monospace;">Response[]</span></dt>
  <dd><p>The list of possible responses as they are returned from executing this operation.</p></dd>
  <dt><strong>callbacks</strong> : <span style="font-family: monospace;">array|null</span></dt>
  <dd><p>A map of possible out-of band callbacks related to the parent operation.<br />
<br />
The key is a unique identifier for the Callback Object.<br />
<br />
Each value in the map is a Callback Object that describes a request that may be initiated by the API provider<br />
and the expected responses. The key value used to identify the callback object is an expression, evaluated at<br />
runtime, that identifies a URL to use for the callback operation.</p></dd>
  <dt><strong>externalDocs</strong> : <span style="font-family: monospace;">OpenApi\Attributes\ExternalDocumentation|null</span></dt>
  <dd><p>Additional external documentation for this operation.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Declares this operation to be deprecated.<br />
<br />
Consumers should refrain from usage of the declared operation.<br />
<br />
Default value is false.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [Xml](https://github.com/zircote/swagger-php/tree/master/src/Attributes/Xml.php)



#### Allowed in
---
<a href="#additionalproperties">AdditionalProperties</a>, <a href="#schema">Schema</a>, <a href="#property">Property</a>, <a href="#schema">Schema</a>, <a href="#items">Items</a>, <a href="#xmlcontent">XmlContent</a>

#### Nested elements
---
<a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>name</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Replaces the name of the element/attribute used for the described schema property.<br />
<br />
When defined within the Items Object (items), it will affect the name of the individual XML elements within the list.<br />
When defined alongside type being array (outside the items), it will affect the wrapping element<br />
and only if wrapped is <code>true</code>.<br />
<br />
If wrapped is <code>false</code>, it will be ignored.</p></dd>
  <dt><strong>namespace</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The URL of the namespace definition. Value SHOULD be in the form of a URL.</p></dd>
  <dt><strong>prefix</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The prefix to be used for the name.</p></dd>
  <dt><strong>attribute</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Declares whether the property definition translates to an attribute instead of an element.<br />
<br />
Default value is <code>false</code>.</p></dd>
  <dt><strong>wrapped</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>MAY be used only for an array definition.<br />
<br />
Signifies whether the array is wrapped (for example  <code>&lt;books>&lt;book/>&lt;book/>&lt;/books></code>)<br />
or unwrapped (<code>&lt;book/>&lt;book/></code>).<br />
<br />
Default value is false. The definition takes effect only when defined alongside type being array (outside the items).</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

## [XmlContent](https://github.com/zircote/swagger-php/tree/master/src/Attributes/XmlContent.php)



#### Nested elements
---
<a href="#discriminator">Discriminator</a>, <a href="#items">Items</a>, <a href="#property">Property</a>, <a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#xml">Xml</a>, <a href="#additionalproperties">AdditionalProperties</a>, <a href="#examples">Examples</a>, <a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>examples</strong> : <span style="font-family: monospace;">array&lt;string,Examples&gt;</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">string|class-string|object|null</span></dt>
  <dd><p>The relative or absolute path to the endpoint.</p><p><i>See</i>: <a href="https://swagger.io/docs/specification/using-ref/">Using refs</a></p></dd>
  <dt><strong>schema</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The key into Components->schemas array.</p></dd>
  <dt><strong>title</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Can be used to decorate a user interface with information about the data produced by this user interface.<br />
<br />
Preferably short; use <code>description</code> for more details.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A description will provide explanation about the purpose of the instance described by this schema.</p></dd>
  <dt><strong>maxProperties</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The maximum number of properties allowed in an object instance.<br />
An object instance is valid against this property if its number of properties is less than, or equal to, the value of this attribute.</p></dd>
  <dt><strong>minProperties</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The minimum number of properties allowed in an object instance.<br />
An object instance is valid against this property if its number of properties is greater than, or equal to, the value of this attribute.</p></dd>
  <dt><strong>required</strong> : <span style="font-family: monospace;">string[]</span></dt>
  <dd><p>An object instance is valid against this property if its property set contains all elements in this property's<br />
array value.</p></dd>
  <dt><strong>properties</strong> : <span style="font-family: monospace;">Property[]</span></dt>
  <dd><p>A collection of properties to define for an object. Each property is represented as an instance of the <a href="#property">Property</a> class.</p></dd>
  <dt><strong>type</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The type of the schema/property. The value MUST be one of "string", "number", "integer", "boolean", "array" or<br />
"object".</p></dd>
  <dt><strong>format</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>The extending format for the previously mentioned type. See Data Type Formats for further details.</p></dd>
  <dt><strong>items</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Items|null</span></dt>
  <dd><p>Required if type is "array". Describes the type of items in the array.</p></dd>
  <dt><strong>collectionFormat</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>Determines the format of the array if type array is used.<br />
Possible values are:<br />
- csv: comma separated values foo,bar.<br />
- ssv: space separated values foo bar.<br />
- tsv: tab separated values foo\tbar.<br />
- pipes: pipe separated values foo|bar.<br />
- multi: corresponds to multiple parameter instances instead of multiple values for a single instance foo=bar&foo=baz.<br />
This is valid only for parameters of type <code>query</code> or <code>formData</code>.<br />
Default value is csv.</p></dd>
  <dt><strong>default</strong> : <span style="font-family: monospace;">mixed|null</span></dt>
  <dd><p>Sets a default value to the parameter. The type of the value depends on the defined type.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor101">JSON schema validation</a></p></dd>
  <dt><strong>maximum</strong> : <span style="font-family: monospace;">int|float</span></dt>
  <dd><p>The maximum value allowed for a numeric property. This value must be a number.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor17">JSON schema validation</a></p></dd>
  <dt><strong>exclusiveMaximum</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>A boolean value indicating whether the maximum value specified in the maximum attribute is excluded from the set of valid values.<br />
If this attribute is set to true, then the maximum value is not included in the set of valid values.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor17">JSON schema validation</a></p></dd>
  <dt><strong>minimum</strong> : <span style="font-family: monospace;">int|float</span></dt>
  <dd><p>The minimum value allowed for a numeric property. This value must be a number.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor21">JSON schema validation</a></p></dd>
  <dt><strong>exclusiveMinimum</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>A boolean value indicating whether the minimum value specified in the minimum attribute is excluded from the set of valid values.<br />
If this attribute is set to true, then the minimum value is not included in the set of valid values.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor21">JSON schema validation</a></p></dd>
  <dt><strong>maxLength</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The maximum length of a string property. A string instance is valid against this property if its length is less than, or equal to, the value of this attribute.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor26">JSON schema validation</a></p></dd>
  <dt><strong>minLength</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The minimum length of a string property. A string instance is valid against this property if its length is greater than, or equal to, the value of this attribute.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor29">JSON schema validation</a></p></dd>
  <dt><strong>maxItems</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The maximum number of items allowed in an array property.<br />
An array instance is valid against this property if its number of items is less than, or equal to, the value of this attribute.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor42">JSON schema validation</a></p></dd>
  <dt><strong>minItems</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>The minimum number of items allowed in an array property.<br />
An array instance is valid against this property if its number of items is greater than, or equal to, the value of this attribute.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor45">JSON schema validation</a></p></dd>
  <dt><strong>uniqueItems</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>A boolean value indicating whether all items in an array property must be unique.<br />
If this attribute is set to true, then all items in the array must be unique.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor49">JSON schema validation</a></p></dd>
  <dt><strong>pattern</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>A string instance is considered valid if the regular expression matches the instance successfully.</p></dd>
  <dt><strong>enum</strong> : <span style="font-family: monospace;">string[]|int[]|float[]|\UnitEnum[]|class-string</span></dt>
  <dd><p>A collection of allowable values for a property.<br />
A property instance is valid against this attribute if its value is one of the values specified in this collection.</p><p><i>See</i>: <a href="http://json-schema.org/latest/json-schema-validation.html#anchor76">JSON schema validation</a></p></dd>
  <dt><strong>discriminator</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Discriminator|null</span></dt>
  <dd><p>Adds support for polymorphism.<br />
<br />
The discriminator is an object name that is used to differentiate between other schemas which may satisfy the<br />
payload description. See Composition and Inheritance for more details.</p></dd>
  <dt><strong>readOnly</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Declares the property as "read only".<br />
<br />
Relevant only for Schema "properties" definitions.<br />
<br />
This means that it may be sent as part of a response but should not be sent as part of the request.<br />
If the property is marked as readOnly being true and is in the required list, the required will take effect on<br />
the response only. A property must not be marked as both readOnly and writeOnly being true. Default value is<br />
false.</p></dd>
  <dt><strong>writeOnly</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Declares the property as "write only".<br />
<br />
Relevant only for Schema "properties" definitions.<br />
Therefore, it may be sent as part of a request but should not be sent as part of the response.<br />
If the property is marked as writeOnly being true and is in the required list, the required will take effect on<br />
the request only. A property must not be marked as both readOnly and writeOnly being true. Default value is<br />
false.</p></dd>
  <dt><strong>xml</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Xml|null</span></dt>
  <dd><p>This may be used only on properties schemas.<br />
<br />
It has no effect on root schemas.<br />
Adds additional metadata to describe the XML representation of this property.</p></dd>
  <dt><strong>externalDocs</strong> : <span style="font-family: monospace;">OpenApi\Attributes\ExternalDocumentation|null</span></dt>
  <dd><p>Additional external documentation for this schema.</p></dd>
  <dt><strong>example</strong> : <span style="font-family: monospace;">mixed|null</span></dt>
  <dd><p>A free-form property to include an example of an instance for this schema.<br />
<br />
To represent examples that cannot naturally be represented in JSON or YAML, a string value can be used to<br />
contain the example with escaping where necessary.</p></dd>
  <dt><strong>nullable</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Allows sending a null value for the defined schema.<br />
Default value is false.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>Specifies that a schema is deprecated and should be transitioned out of usage.<br />
Default value is false.</p></dd>
  <dt><strong>allOf</strong> : <span style="font-family: monospace;">array&lt;Schema|\OpenApi\Annotations\Schema&gt;</span></dt>
  <dd><p>An instance validates successfully against this property if it validates successfully against all schemas<br />
defined by this property's value.</p></dd>
  <dt><strong>anyOf</strong> : <span style="font-family: monospace;">array&lt;Schema|\OpenApi\Annotations\Schema&gt;</span></dt>
  <dd><p>An instance validates successfully against this property if it validates successfully against at least one<br />
schema defined by this property's value.</p></dd>
  <dt><strong>oneOf</strong> : <span style="font-family: monospace;">array&lt;Schema|\OpenApi\Annotations\Schema&gt;</span></dt>
  <dd><p>An instance validates successfully against this property if it validates successfully against exactly one schema<br />
defined by this property's value.</p></dd>
  <dt><strong>additionalProperties</strong> : <span style="font-family: monospace;">OpenApi\Attributes\AdditionalProperties|bool|null</span></dt>
  <dd><p>http://json-schema.org/latest/json-schema-validation.html#anchor64.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;|null</span></dt>
  <dd><p>While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.<br />
For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions<br />
The keys inside the array will be prefixed with `x-`.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>Arbitrary attachables for this annotation.<br />
These will be ignored but can be used for custom processing.</p></dd>
</dl>

