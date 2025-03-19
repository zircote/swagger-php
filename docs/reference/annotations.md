# Annotation Reference

This page is generated automatically from the `swagger-php` sources.

For improvements head over to [GitHub](https://github.com/zircote/swagger-php) and create a PR ;)


In addition to this page, there are also a number of [examples](https://github.com/zircote/swagger-php/tree/master/docs/examples#readme) which might help you out.

## Annotations
### [AdditionalProperties](https://github.com/zircote/swagger-php/tree/master/src/Annotations/AdditionalProperties.php)



#### Allowed in
---
<a href="#schema">Schema</a>, <a href="#property">Property</a>, <a href="#items">Items</a>, <a href="#jsoncontent">JsonContent</a>, <a href="#xmlcontent">XmlContent</a>, <a href="#additionalproperties">AdditionalProperties</a>

#### Nested elements
---
<a href="#discriminator">Discriminator</a>, <a href="#items">Items</a>, <a href="#property">Property</a>, <a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#xml">Xml</a>, <a href="#additionalproperties">AdditionalProperties</a>, <a href="#attachable">Attachable</a>

### [Attachable](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Attachable.php)

A container for custom data to be attached to an annotation.

These will be ignored by `swagger-php` but can be used for custom processing.

#### Allowed in
---
<a href="#additionalproperties">AdditionalProperties</a>, <a href="#components">Components</a>, <a href="#contact">Contact</a>, <a href="#delete">Delete</a>, <a href="#discriminator">Discriminator</a>, <a href="#examples">Examples</a>, <a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#flow">Flow</a>, <a href="#get">Get</a>, <a href="#head">Head</a>, <a href="#header">Header</a>, <a href="#info">Info</a>, <a href="#items">Items</a>, <a href="#jsoncontent">JsonContent</a>, <a href="#license">License</a>, <a href="#link">Link</a>, <a href="#mediatype">MediaType</a>, <a href="#openapi">OpenApi</a>, <a href="#operation">Operation</a>, <a href="#options">Options</a>, <a href="#parameter">Parameter</a>, <a href="#patch">Patch</a>, <a href="#pathitem">PathItem</a>, <a href="#pathparameter">PathParameter</a>, <a href="#post">Post</a>, <a href="#property">Property</a>, <a href="#put">Put</a>, <a href="#requestbody">RequestBody</a>, <a href="#response">Response</a>, <a href="#schema">Schema</a>, <a href="#securityscheme">SecurityScheme</a>, <a href="#server">Server</a>, <a href="#servervariable">ServerVariable</a>, <a href="#tag">Tag</a>, <a href="#trace">Trace</a>, <a href="#webhook">Webhook</a>, <a href="#xml">Xml</a>, <a href="#xmlcontent">XmlContent</a>

### [Components](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Components.php)

Holds a set of reusable objects for different aspects of the OA.

All objects defined within the components object will have no effect on the API unless they are explicitly
referenced from properties outside the components object.

#### Allowed in
---
<a href="#openapi">OpenApi</a>

#### Nested elements
---
<a href="#response">Response</a>, <a href="#parameter">Parameter</a>, <a href="#pathparameter">PathParameter</a>, <a href="#requestbody">RequestBody</a>, <a href="#examples">Examples</a>, <a href="#header">Header</a>, <a href="#securityscheme">SecurityScheme</a>, <a href="#link">Link</a>, <a href="#schema">Schema</a>, <a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>callbacks</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>Reusable Callbacks.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
</dl>

#### Reference
---
- [Components Object](https://spec.openapis.org/oas/v3.1.1.html#components-object)

### [Contact](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Contact.php)

Contact information for the exposed API.

#### Allowed in
---
<a href="#info">Info</a>

#### Nested elements
---
<a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>name</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The identifying name of the contact person/organization.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>url</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The URL pointing to the contact information.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>email</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The email address of the contact person/organization.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
</dl>

#### Reference
---
- [Contact Object](https://spec.openapis.org/oas/v3.1.1.html#components-object)

### [CookieParameter](https://github.com/zircote/swagger-php/tree/master/src/Annotations/CookieParameter.php)

A <code>@OA\Request</code> cookie parameter.

#### Allowed in
---
<a href="#components">Components</a>, <a href="#pathitem">PathItem</a>, <a href="#operation">Operation</a>, <a href="#get">Get</a>, <a href="#post">Post</a>, <a href="#put">Put</a>, <a href="#delete">Delete</a>, <a href="#patch">Patch</a>, <a href="#head">Head</a>, <a href="#options">Options</a>, <a href="#trace">Trace</a>

#### Nested elements
---
<a href="#schema">Schema</a>, <a href="#examples">Examples</a>, <a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>in</strong></dt>
  <dd><p>This takes 'cookie' as the default location.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>yes</b></td></tr></tbody></table></dd>
</dl>

### [Delete](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Delete.php)



#### Allowed in
---
<a href="#pathitem">PathItem</a>

#### Nested elements
---
<a href="#parameter">Parameter</a>, <a href="#pathparameter">PathParameter</a>, <a href="#response">Response</a>, <a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#server">Server</a>, <a href="#requestbody">RequestBody</a>, <a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>method</strong></dt>
  <dd><p>No details available.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
</dl>

### [Discriminator](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Discriminator.php)

The discriminator is a specific object in a schema which is used to inform the consumer of
the specification of an alternative schema based on the value associated with it.

This object is based on the [JSON Schema Specification](http://json-schema.org) and uses a predefined subset of it.
On top of this subset, there are extensions provided by this specification to allow for more complete documentation.

#### Allowed in
---
<a href="#schema">Schema</a>, <a href="#property">Property</a>, <a href="#additionalproperties">AdditionalProperties</a>, <a href="#items">Items</a>, <a href="#jsoncontent">JsonContent</a>, <a href="#xmlcontent">XmlContent</a>

#### Nested elements
---
<a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>propertyName</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The name of the property in the payload that will hold the discriminator value.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>yes</b></td></tr></tbody></table></dd>
  <dt><strong>mapping</strong> : <span style="font-family: monospace;">string[]</span></dt>
  <dd><p>An object to hold mappings between payload values and schema names or references.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
</dl>

#### Reference
---
- [Discriminator Object](https://spec.openapis.org/oas/v3.1.1.html#discriminator-object)
- [JSON Schema](http://json-schema.org/)

### [Examples](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Examples.php)



#### Allowed in
---
<a href="#components">Components</a>, <a href="#schema">Schema</a>, <a href="#parameter">Parameter</a>, <a href="#pathparameter">PathParameter</a>, <a href="#mediatype">MediaType</a>, <a href="#jsoncontent">JsonContent</a>, <a href="#xmlcontent">XmlContent</a>

#### Nested elements
---
<a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">string|class-string|object</span></dt>
  <dd><p>The relative or absolute path to an example.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr><tr><td style="padding-left: 0;"><i>See</i>:</td><td style="padding-left: 0;"><a href="https://spec.openapis.org/oas/v3.1.1.html#reference-object">Reference Object</a></td></tr></tbody></table></dd>
  <dt><strong>example</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The key into <code>#/components/examples</code>.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>summary</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>Short description for the example.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>yes</b></td></tr></tbody></table></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>Embedded literal example.<br />
<br />
The value field and externalValue field are mutually exclusive.<br />
<br />
To represent examples of media types that cannot naturally be represented<br />
in JSON or YAML, use a string value to contain the example, escaping where necessary.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>value</strong> : <span style="font-family: monospace;">int|string|array</span></dt>
  <dd><p>Embedded literal example.<br />
<br />
The value field and externalValue field are mutually exclusive.<br />
<br />
To represent examples of media types that cannot naturally be represented<br />
in JSON or YAML, use a string value to contain the example, escaping where necessary.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>externalValue</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>An URL that points to the literal example.<br />
<br />
This provides the capability to reference examples that cannot easily be included<br />
in JSON or YAML documents.<br />
<br />
The value field and externalValue field are mutually exclusive.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
</dl>

### [ExternalDocumentation](https://github.com/zircote/swagger-php/tree/master/src/Annotations/ExternalDocumentation.php)

Allows referencing an external resource for extended documentation.

#### Allowed in
---
<a href="#openapi">OpenApi</a>, <a href="#tag">Tag</a>, <a href="#schema">Schema</a>, <a href="#additionalproperties">AdditionalProperties</a>, <a href="#property">Property</a>, <a href="#operation">Operation</a>, <a href="#get">Get</a>, <a href="#post">Post</a>, <a href="#put">Put</a>, <a href="#delete">Delete</a>, <a href="#patch">Patch</a>, <a href="#head">Head</a>, <a href="#options">Options</a>, <a href="#trace">Trace</a>, <a href="#items">Items</a>, <a href="#jsoncontent">JsonContent</a>, <a href="#xmlcontent">XmlContent</a>

#### Nested elements
---
<a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>A short description of the target documentation. GFM syntax can be used for rich text representation.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>url</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The URL for the target documentation.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>yes</b></td></tr></tbody></table></dd>
</dl>

#### Reference
---
- [External Documentation Object](https://spec.openapis.org/oas/v3.1.1.html#external-documentation-object)

### [Flow](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Flow.php)

Configuration details for a supported OAuth flow.

#### Allowed in
---
<a href="#securityscheme">SecurityScheme</a>

#### Nested elements
---
<a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>authorizationUrl</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The authorization url to be used for this flow.<br />
<br />
This must be in the form of an url.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>tokenUrl</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The token URL to be used for this flow.<br />
<br />
This must be in the form of an url.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>refreshUrl</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The URL to be used for obtaining refresh tokens.<br />
<br />
This must be in the form of an url.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>flow</strong> : <span style="font-family: monospace;">&#039;authorizationCode&#039;|&#039;clientCredentials&#039;|&#039;implicit&#039;|&#039;password&#039;</span></dt>
  <dd><p>Flow name.<br />
<br />
One of ['implicit', 'password', 'authorizationCode', 'clientCredentials'].</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>yes</b></td></tr></tbody></table></dd>
  <dt><strong>scopes</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>The available scopes for the OAuth2 security scheme.<br />
<br />
A map between the scope name and a short description for it.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>yes</b></td></tr></tbody></table></dd>
</dl>

#### Reference
---
- [OAuth Flow Object](https://spec.openapis.org/oas/v3.1.1.html#oauth-flow-object)

### [Get](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Get.php)



#### Allowed in
---
<a href="#pathitem">PathItem</a>

#### Nested elements
---
<a href="#parameter">Parameter</a>, <a href="#pathparameter">PathParameter</a>, <a href="#response">Response</a>, <a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#server">Server</a>, <a href="#requestbody">RequestBody</a>, <a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>method</strong></dt>
  <dd><p>No details available.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
</dl>

### [Head](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Head.php)



#### Allowed in
---
<a href="#pathitem">PathItem</a>

#### Nested elements
---
<a href="#parameter">Parameter</a>, <a href="#pathparameter">PathParameter</a>, <a href="#response">Response</a>, <a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#server">Server</a>, <a href="#requestbody">RequestBody</a>, <a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>method</strong></dt>
  <dd><p>No details available.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
</dl>

### [Header](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Header.php)



#### Allowed in
---
<a href="#components">Components</a>, <a href="#response">Response</a>

#### Nested elements
---
<a href="#schema">Schema</a>, <a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">string|class-string|object</span></dt>
  <dd><p>The relative or absolute path to the endpoint.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr><tr><td style="padding-left: 0;"><i>See</i>:</td><td style="padding-left: 0;"><a href="https://spec.openapis.org/oas/v3.1.1.html#reference-object">Reference Object</a></td></tr></tbody></table></dd>
  <dt><strong>header</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>No details available.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>yes</b></td></tr></tbody></table></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>A brief description of the parameter.<br />
<br />
This could contain examples of use.<br />
CommonMark syntax MAY be used for rich text representation.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>required</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>No details available.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>Specifies that a parameter is deprecated and SHOULD be transitioned out of usage.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>allowEmptyValue</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>Sets the ability to pass empty-valued parameters.<br />
<br />
This is valid only for query parameters and allows sending a parameter with an empty value.<br />
<br />
Default value is false.<br />
<br />
If style is used, and if behavior is n/a (cannot be serialized), the value of allowEmptyValue SHALL be ignored.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
</dl>

#### Reference
---
- [Header Object](https://spec.openapis.org/oas/v3.1.1.html#header-object)

### [HeaderParameter](https://github.com/zircote/swagger-php/tree/master/src/Annotations/HeaderParameter.php)

A <code>@OA\Request</code> header parameter.

#### Allowed in
---
<a href="#components">Components</a>, <a href="#pathitem">PathItem</a>, <a href="#operation">Operation</a>, <a href="#get">Get</a>, <a href="#post">Post</a>, <a href="#put">Put</a>, <a href="#delete">Delete</a>, <a href="#patch">Patch</a>, <a href="#head">Head</a>, <a href="#options">Options</a>, <a href="#trace">Trace</a>

#### Nested elements
---
<a href="#schema">Schema</a>, <a href="#examples">Examples</a>, <a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>in</strong></dt>
  <dd><p>This takes 'header' as the default location.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>yes</b></td></tr></tbody></table></dd>
</dl>

### [Info](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Info.php)

The object provides metadata about the API.

The metadata may be used by the clients if needed and may be presented in editing or documentation generation tools for convenience.

#### Allowed in
---
<a href="#openapi">OpenApi</a>

#### Nested elements
---
<a href="#contact">Contact</a>, <a href="#license">License</a>, <a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>title</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The title of the application.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>yes</b></td></tr></tbody></table></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>A short description of the application.<br />
<br />
CommonMark syntax may be used for rich text representation.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>termsOfService</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>An URL to the Terms of Service for the API.<br />
<br />
Must be in the format of an url.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>version</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The version of the OpenAPI document (which is distinct from the OpenAPI Specification version or the API implementation version).</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>yes</b></td></tr></tbody></table></dd>
</dl>

#### Reference
---
- [Info Object](https://spec.openapis.org/oas/v3.1.1.html#info-object)

### [Items](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Items.php)

The description of an item in a Schema with type <code>array</code>.

#### Allowed in
---
<a href="#property">Property</a>, <a href="#additionalproperties">AdditionalProperties</a>, <a href="#schema">Schema</a>, <a href="#jsoncontent">JsonContent</a>, <a href="#xmlcontent">XmlContent</a>, <a href="#items">Items</a>

#### Nested elements
---
<a href="#discriminator">Discriminator</a>, <a href="#items">Items</a>, <a href="#property">Property</a>, <a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#xml">Xml</a>, <a href="#additionalproperties">AdditionalProperties</a>, <a href="#attachable">Attachable</a>

### [JsonContent](https://github.com/zircote/swagger-php/tree/master/src/Annotations/JsonContent.php)

Shorthand for a json response.

Example:
```php
@OA\JsonContent(
    ref="#/components/schemas/user"
)
```
vs.
```php
@OA\MediaType(
    mediaType="application/json",
    @OA\Schema(
        ref="#/components/schemas/user"
    )
)
```

#### Nested elements
---
<a href="#discriminator">Discriminator</a>, <a href="#items">Items</a>, <a href="#property">Property</a>, <a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#additionalproperties">AdditionalProperties</a>, <a href="#examples">Examples</a>, <a href="#attachable">Attachable</a>

### [License](https://github.com/zircote/swagger-php/tree/master/src/Annotations/License.php)

License information for the exposed API.

#### Allowed in
---
<a href="#info">Info</a>

#### Nested elements
---
<a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>name</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The license name used for the API.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>yes</b></td></tr></tbody></table></dd>
  <dt><strong>identifier</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>An SPDX license expression for the API. The <code>identifier</code> field is mutually exclusive of the <code>url</code> field.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>url</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>A URL to the license used for the API. This MUST be in the form of a URL.<br />
<br />
The <code>url</code> field is mutually exclusive of the <code>identifier</code> field.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
</dl>

#### Reference
---
- [License Object](https://spec.openapis.org/oas/v3.1.1.html#license-object)

### [Link](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Link.php)

The Link object represents a possible design-time link for a response.

The presence of a link does not guarantee the caller's ability to successfully invoke it, rather it provides a known
relationship and traversal mechanism between responses and other operations.

Unlike dynamic links (i.e. links provided in the response payload), the OA linking mechanism does not require
link information in the runtime response.

For computing links, and providing instructions to execute them, a runtime expression is used for
accessing values in an operation and using them as parameters while invoking the linked operation.

#### Allowed in
---
<a href="#components">Components</a>, <a href="#response">Response</a>

#### Nested elements
---
<a href="#server">Server</a>, <a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">string|class-string|object</span></dt>
  <dd><p>No details available.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr><tr><td style="padding-left: 0;"><i>See</i>:</td><td style="padding-left: 0;"><a href="https://spec.openapis.org/oas/v3.1.1.html#reference-object">Reference Object</a></td></tr></tbody></table></dd>
  <dt><strong>link</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The key into MediaType->links array.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>operationRef</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>A relative or absolute reference to an OA operation.<br />
<br />
This field is mutually exclusive of the <code>operationId</code> field, and must point to an Operation object.<br />
<br />
Relative values may be used to locate an existing Operation object in the OpenAPI definition.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>operationId</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The name of an existing, resolvable OA operation, as defined with a unique <code>operationId</code>.<br />
<br />
This field is mutually exclusive of the <code>operationRef</code> field.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>parameters</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;</span></dt>
  <dd><p>A map representing parameters to pass to an operation as specified with operationId or identified via<br />
operationRef.<br />
<br />
The key is the parameter name to be used, whereas the value can be a constant or an expression to<br />
be evaluated and passed to the linked operation.<br />
The parameter name can be qualified using the parameter location [{in}.]{name} for operations<br />
that use the same parameter name in different locations (e.g. path.id).</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>requestBody</strong></dt>
  <dd><p>A literal value or {expression} to use as a request body when calling the target operation.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>A description of the link.<br />
<br />
CommonMark syntax may be used for rich text representation.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
</dl>

#### Reference
---
- [Link Object](https://spec.openapis.org/oas/v3.1.1.html#link-object)

### [MediaType](https://github.com/zircote/swagger-php/tree/master/src/Annotations/MediaType.php)

Each Media Type object provides schema and examples for the media type identified by its key.

#### Allowed in
---
<a href="#response">Response</a>, <a href="#requestbody">RequestBody</a>

#### Nested elements
---
<a href="#schema">Schema</a>, <a href="#examples">Examples</a>, <a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>mediaType</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The key into Operation->content array.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>example</strong></dt>
  <dd><p>Example of the media type.<br />
<br />
The example object should be in the correct format as specified by the media type.<br />
The example object is mutually exclusive of the examples object.<br />
<br />
Furthermore, if referencing a schema which contains an example,<br />
the example value shall override the example provided by the schema.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>encoding</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;</span></dt>
  <dd><p>A map between a property name and its encoding information.<br />
<br />
The key, being the property name, must exist in the schema as a property.<br />
<br />
The encoding object shall only apply to requestBody objects when the media type is multipart or<br />
application/x-www-form-urlencoded.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
</dl>

#### Reference
---
- [Media Type Object](https://spec.openapis.org/oas/v3.1.1.html#media-type-object)

### [OpenApi](https://github.com/zircote/swagger-php/tree/master/src/Annotations/OpenApi.php)

This is the root document object for the API specification.

#### Nested elements
---
<a href="#info">Info</a>, <a href="#server">Server</a>, <a href="#pathitem">PathItem</a>, <a href="#components">Components</a>, <a href="#tag">Tag</a>, <a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#webhook">Webhook</a>, <a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>openapi</strong> : <span style="font-family: monospace;">&#039;3.0.0&#039;|&#039;3.1.0&#039;</span></dt>
  <dd><p>The semantic version number of the OpenAPI Specification version that the OpenAPI document uses.<br />
<br />
The openapi field should be used by tooling specifications and clients to interpret the OpenAPI document.<br />
<br />
A version specified via <code>Generator::setVersion()</code> will overwrite this value.<br />
<br />
This is not related to the API info::version string.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>yes</b></td></tr></tbody></table></dd>
  <dt><strong>security</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>A declaration of which security mechanisms can be used across the API.<br />
<br />
The list of values includes alternative security requirement objects that can be used.<br />
Only one of the security requirement objects need to be satisfied to authorize a request.<br />
Individual operations can override this definition.<br />
To make security optional, an empty security requirement (<code>{}</code>) can be included in the array.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
</dl>

#### Reference
---
- [OpenApi Object](https://spec.openapis.org/oas/v3.1.1.html#openapi-object)

### [Options](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Options.php)



#### Allowed in
---
<a href="#pathitem">PathItem</a>

#### Nested elements
---
<a href="#parameter">Parameter</a>, <a href="#pathparameter">PathParameter</a>, <a href="#response">Response</a>, <a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#server">Server</a>, <a href="#requestbody">RequestBody</a>, <a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>method</strong></dt>
  <dd><p>No details available.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
</dl>

### [Parameter](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Parameter.php)

Describes a single operation parameter.

A unique parameter is defined by a combination of a name and location.

#### Allowed in
---
<a href="#components">Components</a>, <a href="#pathitem">PathItem</a>, <a href="#operation">Operation</a>, <a href="#get">Get</a>, <a href="#post">Post</a>, <a href="#put">Put</a>, <a href="#delete">Delete</a>, <a href="#patch">Patch</a>, <a href="#head">Head</a>, <a href="#options">Options</a>, <a href="#trace">Trace</a>

#### Nested elements
---
<a href="#schema">Schema</a>, <a href="#examples">Examples</a>, <a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">string|class-string|object</span></dt>
  <dd><p>The relative or absolute path to the endpoint.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr><tr><td style="padding-left: 0;"><i>See</i>:</td><td style="padding-left: 0;"><a href="https://spec.openapis.org/oas/v3.1.1.html#reference-object">Reference Object</a></td></tr></tbody></table></dd>
  <dt><strong>parameter</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The key into <code>Components::parameters</code> or <code>PathItem::parameters</code> array.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>name</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The (case-sensitive) name of the parameter.<br />
<br />
If in is "path", the name field must correspond to the associated path segment from the path field in the Paths Object.<br />
<br />
If in is "header" and the name field is "Accept", "Content-Type" or "Authorization", the parameter definition shall be ignored.<br />
For all other cases, the name corresponds to the parameter name used by the in property.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>yes</b></td></tr></tbody></table></dd>
  <dt><strong>in</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The location of the parameter.<br />
<br />
Possible values are "query", "header", "path" or "cookie".</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>yes</b></td></tr></tbody></table></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>A brief description of the parameter.<br />
<br />
This could contain examples of use.<br />
<br />
CommonMark syntax may be used for rich text representation.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>required</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>Determines whether this parameter is mandatory.<br />
<br />
If the parameter location is "path", this property is required and its value must be true.<br />
Otherwise, the property may be included and its default value is false.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>Specifies that a parameter is deprecated and should be transitioned out of usage.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>allowEmptyValue</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>Sets the ability to pass empty-valued parameters.<br />
<br />
This is valid only for query parameters and allows sending a parameter with an empty value.<br />
<br />
Default value is false.<br />
<br />
If style is used, and if behavior is n/a (cannot be serialized), the value of allowEmptyValue shall be ignored.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>style</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>Describes how the parameter value will be serialized depending on the type of the parameter value.<br />
<br />
Default values (based on value of in): for query - form; for path - simple; for header - simple; for cookie - form.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>explode</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>When this is true, parameter values of type array or object generate separate parameters for each value of the array or key-value pair of the map.<br />
<br />
For other types of parameters this property has no effect.<br />
<br />
When style is form, the default value is true.<br />
For all other styles, the default value is false.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>allowReserved</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>Determines whether the parameter value should allow reserved characters, as defined by RFC3986 :/?#[]@!$&'()*+,;= to be included without percent-encoding.<br />
<br />
This property only applies to parameters with an in value of query.<br />
<br />
The default value is false.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>example</strong></dt>
  <dd><p>Example of the media type.<br />
<br />
The example should match the specified schema and encoding properties if present.<br />
The example object is mutually exclusive of the examples object.<br />
Furthermore, if referencing a schema which contains an example, the example value shall override the example provided by the schema.<br />
To represent examples of media types that cannot naturally be represented in JSON or YAML, a string value can contain the example with escaping where necessary.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>content</strong> : <span style="font-family: monospace;">array&lt;MediaType&gt;|JsonContent|XmlContent|Attachable</span></dt>
  <dd><p>A map containing the representations for the parameter.<br />
<br />
The key is the media type and the value describes it.<br />
The map must only contain one entry.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>matrix</strong></dt>
  <dd><p>Path-style parameters defined by RFC6570.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr><tr><td style="padding-left: 0;"><i>See</i>:</td><td style="padding-left: 0;"><a href="https://tools.ietf.org/html/rfc6570#section-3.2.7">RFC6570</a></td></tr></tbody></table></dd>
  <dt><strong>label</strong></dt>
  <dd><p>Label style parameters defined by RFC6570.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr><tr><td style="padding-left: 0;"><i>See</i>:</td><td style="padding-left: 0;"><a href="https://tools.ietf.org/html/rfc6570#section-3.2.5">RFC6570</a></td></tr></tbody></table></dd>
  <dt><strong>form</strong></dt>
  <dd><p>Form style parameters defined by RFC6570.<br />
<br />
This option replaces collectionFormat with a csv (when explode is false) or multi (when explode is true) value from OpenAPI 2.0.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr><tr><td style="padding-left: 0;"><i>See</i>:</td><td style="padding-left: 0;"><a href="https://tools.ietf.org/html/rfc6570#section-3.2.8">RFC6570</a></td></tr></tbody></table></dd>
  <dt><strong>simple</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>Simple style parameters defined by RFC6570.<br />
<br />
This option replaces collectionFormat with a csv value from OpenAPI 2.0.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr><tr><td style="padding-left: 0;"><i>See</i>:</td><td style="padding-left: 0;"><a href="https://tools.ietf.org/html/rfc6570#section-3.2.2">RFC6570</a></td></tr></tbody></table></dd>
  <dt><strong>spaceDelimited</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>Space separated array values.<br />
<br />
This option replaces collectionFormat equal to ssv from OpenAPI 2.0.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>pipeDelimited</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>Pipe separated array values.<br />
<br />
This option replaces collectionFormat equal to pipes from OpenAPI 2.0.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>deepObject</strong></dt>
  <dd><p>Provides a simple way of rendering nested objects using form parameters.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
</dl>

#### Reference
---
- [Parameter Object](https://spec.openapis.org/oas/v3.1.1.html#parameter-object)

### [Patch](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Patch.php)



#### Allowed in
---
<a href="#pathitem">PathItem</a>

#### Nested elements
---
<a href="#parameter">Parameter</a>, <a href="#pathparameter">PathParameter</a>, <a href="#response">Response</a>, <a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#server">Server</a>, <a href="#requestbody">RequestBody</a>, <a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>method</strong></dt>
  <dd><p>No details available.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
</dl>

### [PathItem](https://github.com/zircote/swagger-php/tree/master/src/Annotations/PathItem.php)

Describes the operations available on a single path.

A Path Item may be empty, due to ACL constraints.
The path itself is still exposed to the documentation viewer, but they will not know which operations and parameters are available.

#### Allowed in
---
<a href="#openapi">OpenApi</a>

#### Nested elements
---
<a href="#get">Get</a>, <a href="#post">Post</a>, <a href="#put">Put</a>, <a href="#delete">Delete</a>, <a href="#patch">Patch</a>, <a href="#trace">Trace</a>, <a href="#head">Head</a>, <a href="#options">Options</a>, <a href="#parameter">Parameter</a>, <a href="#pathparameter">PathParameter</a>, <a href="#server">Server</a>, <a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">string|class-string|object</span></dt>
  <dd><p>No details available.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr><tr><td style="padding-left: 0;"><i>See</i>:</td><td style="padding-left: 0;"><a href="https://spec.openapis.org/oas/v3.1.1.html#reference-object">Reference Object</a></td></tr></tbody></table></dd>
  <dt><strong>summary</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>An optional, string summary, intended to apply to all operations in this path.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>An optional, string description, intended to apply to all operations in this path.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>path</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>Key for the Path Object (OpenApi->paths array).</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
</dl>

#### Reference
---
- [Path Item Object](https://spec.openapis.org/oas/v3.1.1.html#path-item-object)

### [PathParameter](https://github.com/zircote/swagger-php/tree/master/src/Annotations/PathParameter.php)

A <code>@OA\Request</code> path parameter.

#### Allowed in
---
<a href="#components">Components</a>, <a href="#pathitem">PathItem</a>, <a href="#operation">Operation</a>, <a href="#get">Get</a>, <a href="#post">Post</a>, <a href="#put">Put</a>, <a href="#delete">Delete</a>, <a href="#patch">Patch</a>, <a href="#head">Head</a>, <a href="#options">Options</a>, <a href="#trace">Trace</a>

#### Nested elements
---
<a href="#schema">Schema</a>, <a href="#examples">Examples</a>, <a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>in</strong></dt>
  <dd><p>This takes 'path' as the default location.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>required</strong></dt>
  <dd><p>No details available.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
</dl>

### [Post](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Post.php)



#### Allowed in
---
<a href="#pathitem">PathItem</a>

#### Nested elements
---
<a href="#parameter">Parameter</a>, <a href="#pathparameter">PathParameter</a>, <a href="#response">Response</a>, <a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#server">Server</a>, <a href="#requestbody">RequestBody</a>, <a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>method</strong></dt>
  <dd><p>No details available.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
</dl>

### [Property](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Property.php)



#### Allowed in
---
<a href="#additionalproperties">AdditionalProperties</a>, <a href="#schema">Schema</a>, <a href="#jsoncontent">JsonContent</a>, <a href="#xmlcontent">XmlContent</a>, <a href="#property">Property</a>, <a href="#items">Items</a>

#### Nested elements
---
<a href="#discriminator">Discriminator</a>, <a href="#items">Items</a>, <a href="#property">Property</a>, <a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#xml">Xml</a>, <a href="#additionalproperties">AdditionalProperties</a>, <a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>property</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The key into Schema->properties array.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
</dl>

### [Put](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Put.php)



#### Allowed in
---
<a href="#pathitem">PathItem</a>

#### Nested elements
---
<a href="#parameter">Parameter</a>, <a href="#pathparameter">PathParameter</a>, <a href="#response">Response</a>, <a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#server">Server</a>, <a href="#requestbody">RequestBody</a>, <a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>method</strong></dt>
  <dd><p>No details available.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
</dl>

### [QueryParameter](https://github.com/zircote/swagger-php/tree/master/src/Annotations/QueryParameter.php)

A <code>@OA\Request</code> query parameter.

#### Allowed in
---
<a href="#components">Components</a>, <a href="#pathitem">PathItem</a>, <a href="#operation">Operation</a>, <a href="#get">Get</a>, <a href="#post">Post</a>, <a href="#put">Put</a>, <a href="#delete">Delete</a>, <a href="#patch">Patch</a>, <a href="#head">Head</a>, <a href="#options">Options</a>, <a href="#trace">Trace</a>

#### Nested elements
---
<a href="#schema">Schema</a>, <a href="#examples">Examples</a>, <a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>in</strong></dt>
  <dd><p>This takes 'query' as the default location.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>yes</b></td></tr></tbody></table></dd>
</dl>

### [RequestBody](https://github.com/zircote/swagger-php/tree/master/src/Annotations/RequestBody.php)

Describes a single request body.

#### Allowed in
---
<a href="#components">Components</a>, <a href="#delete">Delete</a>, <a href="#get">Get</a>, <a href="#head">Head</a>, <a href="#operation">Operation</a>, <a href="#options">Options</a>, <a href="#patch">Patch</a>, <a href="#post">Post</a>, <a href="#trace">Trace</a>, <a href="#put">Put</a>

#### Nested elements
---
<a href="#mediatype">MediaType</a>, <a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">string|class-string|object</span></dt>
  <dd><p>The relative or absolute path to a request body.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr><tr><td style="padding-left: 0;"><i>See</i>:</td><td style="padding-left: 0;"><a href="https://spec.openapis.org/oas/v3.1.1.html#reference-object">Reference Object</a></td></tr></tbody></table></dd>
  <dt><strong>request</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The key into Components->requestBodies array.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>A brief description of the parameter.<br />
<br />
This could contain examples of use.<br />
<br />
CommonMark syntax may be used for rich text representation.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>required</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>Determines whether this parameter is mandatory.<br />
<br />
If the parameter location is "path", this property is required and its value must be true.<br />
Otherwise, the property may be included and its default value is false.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
</dl>

#### Reference
---
- [Request Body Object](https://spec.openapis.org/oas/v3.1.1.html#request-body-object)

### [Response](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Response.php)

Describes a single response from an API Operation, including design-time,
static links to operations based on the response.

#### Allowed in
---
<a href="#components">Components</a>, <a href="#operation">Operation</a>, <a href="#get">Get</a>, <a href="#post">Post</a>, <a href="#put">Put</a>, <a href="#patch">Patch</a>, <a href="#delete">Delete</a>, <a href="#head">Head</a>, <a href="#options">Options</a>, <a href="#trace">Trace</a>

#### Nested elements
---
<a href="#mediatype">MediaType</a>, <a href="#header">Header</a>, <a href="#link">Link</a>, <a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">string|class-string|object</span></dt>
  <dd><p>The relative or absolute path to a response.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr><tr><td style="padding-left: 0;"><i>See</i>:</td><td style="padding-left: 0;"><a href="https://spec.openapis.org/oas/v3.1.1.html#reference-object">Reference Object</a></td></tr></tbody></table></dd>
  <dt><strong>response</strong> : <span style="font-family: monospace;">string|int</span></dt>
  <dd><p>The key into Operations->responses array.<br />
<br />
A HTTP status code or <code>default</code>.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>A short description of the response.<br />
<br />
CommonMark syntax may be used for rich text representation.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
</dl>

#### Reference
---
- [Response Object](https://spec.openapis.org/oas/v3.1.1.html#response-object)

### [Schema](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Schema.php)

The definition of input and output data types.

These types can be objects, but also primitives and arrays.

This object is based on the [JSON Schema Specification](http://json-schema.org) and uses a predefined subset of it.
On top of this subset, there are extensions provided by this specification to allow for more complete documentation.

#### Allowed in
---
<a href="#components">Components</a>, <a href="#parameter">Parameter</a>, <a href="#pathparameter">PathParameter</a>, <a href="#mediatype">MediaType</a>, <a href="#header">Header</a>

#### Nested elements
---
<a href="#discriminator">Discriminator</a>, <a href="#items">Items</a>, <a href="#property">Property</a>, <a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#examples">Examples</a>, <a href="#xml">Xml</a>, <a href="#additionalproperties">AdditionalProperties</a>, <a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">string|class-string|object</span></dt>
  <dd><p>The relative or absolute path to the endpoint.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr><tr><td style="padding-left: 0;"><i>See</i>:</td><td style="padding-left: 0;"><a href="https://spec.openapis.org/oas/v3.1.1.html#reference-object">Reference Object</a></td></tr></tbody></table></dd>
  <dt><strong>schema</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The key into Components->schemas array.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>title</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>Can be used to decorate a user interface with information about the data produced by this user interface.<br />
<br />
Preferably short; use <code>description</code> for more details.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>A description will provide explanation about the purpose of the instance described by this schema.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>maxProperties</strong> : <span style="font-family: monospace;">int</span></dt>
  <dd><p>The maximum number of properties allowed in an object instance.<br />
An object instance is valid against this property if its number of properties is less than, or equal to, the<br />
value of this attribute.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>minProperties</strong> : <span style="font-family: monospace;">int</span></dt>
  <dd><p>The minimum number of properties allowed in an object instance.<br />
An object instance is valid against this property if its number of properties is greater than, or equal to, the<br />
value of this attribute.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>required</strong> : <span style="font-family: monospace;">string[]</span></dt>
  <dd><p>An object instance is valid against this property if its property set contains all elements in this property's<br />
array value.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>type</strong> : <span style="font-family: monospace;">string|non-empty-array&lt;string&gt;</span></dt>
  <dd><p>The type of the schema/property.<br />
<br />
OpenApi v3.0: The value MUST be one of "string", "number", "integer", "boolean", "array" or "object".<br />
<br />
Since OpenApi v3.1 an array of types may be used.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>format</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The extending format for the previously mentioned type.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr><tr><td style="padding-left: 0;"><i>See</i>:</td><td style="padding-left: 0;"><a href="https://spec.openapis.org/oas/v3.1.1.html#data-types">Data Types</a></td></tr></tbody></table></dd>
  <dt><strong>collectionFormat</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>Determines the format of the array if type array is used.<br />
<br />
Possible values are:<br />
- csv: comma separated values foo,bar.<br />
- ssv: space separated values foo bar.<br />
- tsv: tab separated values foo\tbar.<br />
- pipes: pipe separated values foo|bar.<br />
- multi: corresponds to multiple parameter instances instead of multiple values for a single instance<br />
foo=bar&foo=baz. This is valid only for parameters of type <code>query</code> or <code>formData</code>. Default<br />
value is csv.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>default</strong></dt>
  <dd><p>Sets a default value to the parameter. The type of the value depends on the defined type.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr><tr><td style="padding-left: 0;"><i>See</i>:</td><td style="padding-left: 0;"><a href="http://json-schema.org/latest/json-schema-validation.html#anchor101">JSON schema validation</a></td></tr></tbody></table></dd>
  <dt><strong>maximum</strong> : <span style="font-family: monospace;">int|float</span></dt>
  <dd><p>The maximum value allowed for a numeric property. This value must be a number.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr><tr><td style="padding-left: 0;"><i>See</i>:</td><td style="padding-left: 0;"><a href="http://json-schema.org/latest/json-schema-validation.html#anchor17">JSON schema validation</a></td></tr></tbody></table></dd>
  <dt><strong>exclusiveMaximum</strong> : <span style="font-family: monospace;">bool|int|float</span></dt>
  <dd><p>A boolean indicating whether the maximum value is excluded from the set of valid values.<br />
<br />
When set to true, the maximum value is excluded, and when false or not specified, it is included.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr><tr><td style="padding-left: 0;"><i>See</i>:</td><td style="padding-left: 0;"><a href="http://json-schema.org/latest/json-schema-validation.html#anchor17">JSON schema validation</a></td></tr></tbody></table></dd>
  <dt><strong>minimum</strong> : <span style="font-family: monospace;">int|float</span></dt>
  <dd><p>The minimum value allowed for a numeric property. This value must be a number.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr><tr><td style="padding-left: 0;"><i>See</i>:</td><td style="padding-left: 0;"><a href="http://json-schema.org/latest/json-schema-validation.html#anchor21">JSON schema validation</a></td></tr></tbody></table></dd>
  <dt><strong>exclusiveMinimum</strong> : <span style="font-family: monospace;">bool|int|float</span></dt>
  <dd><p>A boolean indicating whether the minimum value is excluded from the set of valid values.<br />
<br />
When set to true, the minimum value is excluded, and when false or not specified, it is included.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr><tr><td style="padding-left: 0;"><i>See</i>:</td><td style="padding-left: 0;"><a href="http://json-schema.org/latest/json-schema-validation.html#anchor21">JSON schema validation</a></td></tr></tbody></table></dd>
  <dt><strong>maxLength</strong> : <span style="font-family: monospace;">int</span></dt>
  <dd><p>The maximum length of a string property.<br />
<br />
A string instance is valid against this property if its length is less than, or equal to, the value of this<br />
attribute.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr><tr><td style="padding-left: 0;"><i>See</i>:</td><td style="padding-left: 0;"><a href="http://json-schema.org/latest/json-schema-validation.html#anchor26">JSON schema validation</a></td></tr></tbody></table></dd>
  <dt><strong>minLength</strong> : <span style="font-family: monospace;">int</span></dt>
  <dd><p>The minimum length of a string property.<br />
<br />
A string instance is valid against this property if its length is greater than, or equal to, the value of this<br />
attribute.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr><tr><td style="padding-left: 0;"><i>See</i>:</td><td style="padding-left: 0;"><a href="http://json-schema.org/latest/json-schema-validation.html#anchor29">JSON schema validation</a></td></tr></tbody></table></dd>
  <dt><strong>pattern</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>A string instance is considered valid if the regular expression matches the instance successfully.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>maxItems</strong> : <span style="font-family: monospace;">int</span></dt>
  <dd><p>The maximum number of items allowed in an array property.<br />
<br />
An array instance is valid against this property if its number of items is less than, or equal to, the value of<br />
this attribute.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr><tr><td style="padding-left: 0;"><i>See</i>:</td><td style="padding-left: 0;"><a href="http://json-schema.org/latest/json-schema-validation.html#anchor42">JSON schema validation</a></td></tr></tbody></table></dd>
  <dt><strong>minItems</strong> : <span style="font-family: monospace;">int</span></dt>
  <dd><p>The minimum number of items allowed in an array property.<br />
<br />
An array instance is valid against this property if its number of items is greater than, or equal to, the value<br />
of this attribute.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr><tr><td style="padding-left: 0;"><i>See</i>:</td><td style="padding-left: 0;"><a href="http://json-schema.org/latest/json-schema-validation.html#anchor45">JSON schema validation</a></td></tr></tbody></table></dd>
  <dt><strong>uniqueItems</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>A boolean value indicating whether all items in an array property must be unique.<br />
<br />
If this attribute is set to true, then all items in the array must be unique.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr><tr><td style="padding-left: 0;"><i>See</i>:</td><td style="padding-left: 0;"><a href="http://json-schema.org/latest/json-schema-validation.html#anchor49">JSON schema validation</a></td></tr></tbody></table></dd>
  <dt><strong>enum</strong> : <span style="font-family: monospace;">array&lt;string|int|float|bool|\UnitEnum&gt;|class-string</span></dt>
  <dd><p>A collection of allowable values for a property.<br />
<br />
A property instance is valid against this attribute if its value is one of the values specified in this<br />
collection.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr><tr><td style="padding-left: 0;"><i>See</i>:</td><td style="padding-left: 0;"><a href="http://json-schema.org/latest/json-schema-validation.html#anchor76">JSON schema validation</a></td></tr></tbody></table></dd>
  <dt><strong>multipleOf</strong> : <span style="font-family: monospace;">int|float</span></dt>
  <dd><p>A numeric instance is valid against "multipleOf" if the result of the division of the instance by this<br />
property's value is an integer.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>readOnly</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>Declares the property as "read only".<br />
<br />
Relevant only for Schema "properties" definitions.<br />
<br />
This means that it may be sent as part of a response but should not be sent as part of the request.<br />
If the property is marked as readOnly being true and is in the required list, the required will take effect on<br />
the response only. A property must not be marked as both readOnly and writeOnly being true. Default value is<br />
false.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>writeOnly</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>Declares the property as "write only".<br />
<br />
Relevant only for Schema "properties" definitions.<br />
Therefore, it may be sent as part of a request but should not be sent as part of the response.<br />
If the property is marked as writeOnly being true and is in the required list, the required will take effect on<br />
the request only. A property must not be marked as both readOnly and writeOnly being true. Default value is<br />
false.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>example</strong></dt>
  <dd><p>A free-form property to include an example of an instance for this schema.<br />
<br />
To represent examples that cannot naturally be represented in JSON or YAML, a string value can be used to<br />
contain the example with escaping where necessary.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>nullable</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>Allows sending a null value for the defined schema.<br />
Default value is false.<br />
<br />
This must not be used when using OpenApi version 3.1,<br />
instead make the "type" property an array and add "null" as a possible type.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>Specifies that a schema is deprecated and should be transitioned out of usage.<br />
Default value is false.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>allOf</strong> : <span style="font-family: monospace;">array&lt;Schema|\OpenApi\Attributes\Schema&gt;</span></dt>
  <dd><p>An instance validates successfully against this property if it validates successfully against all schemas<br />
defined by this property's value.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>anyOf</strong> : <span style="font-family: monospace;">array&lt;Schema|\OpenApi\Attributes\Schema&gt;</span></dt>
  <dd><p>An instance validates successfully against this property if it validates successfully against at least one<br />
schema defined by this property's value.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>oneOf</strong> : <span style="font-family: monospace;">array&lt;Schema|\OpenApi\Attributes\Schema&gt;</span></dt>
  <dd><p>An instance validates successfully against this property if it validates successfully against exactly one schema<br />
defined by this property's value.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>not</strong></dt>
  <dd><p>http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.29.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>additionalItems</strong></dt>
  <dd><p>http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.10.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>contains</strong></dt>
  <dd><p>http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.14.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>patternProperties</strong></dt>
  <dd><p>http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.19.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>dependencies</strong></dt>
  <dd><p>http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.21.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>propertyNames</strong></dt>
  <dd><p>http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.22.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>const</strong></dt>
  <dd><p>http://json-schema.org/draft/2020-12/json-schema-validation.html#rfc.section.6.1.3.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
</dl>

#### Reference
---
- [Schema Object](https://spec.openapis.org/oas/v3.1.1.html#schema-object)
- [JSON Schema](http://json-schema.org/)

### [SecurityScheme](https://github.com/zircote/swagger-php/tree/master/src/Annotations/SecurityScheme.php)



#### Allowed in
---
<a href="#components">Components</a>

#### Nested elements
---
<a href="#flow">Flow</a>, <a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">string|class-string|object</span></dt>
  <dd><p>The relative or absolute path to a security scheme.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr><tr><td style="padding-left: 0;"><i>See</i>:</td><td style="padding-left: 0;"><a href="https://spec.openapis.org/oas/v3.1.1.html#reference-object">Reference Object</a></td></tr></tbody></table></dd>
  <dt><strong>securityScheme</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The key into OpenApi->security array.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>yes</b></td></tr></tbody></table></dd>
  <dt><strong>type</strong> : <span style="font-family: monospace;">string|non-empty-array&lt;string&gt;</span></dt>
  <dd><p>The type of the security scheme.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>yes</b></td></tr></tbody></table></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>A short description for security scheme.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>name</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The name of the header or query parameter to be used.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>in</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>Required The location of the API key.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>bearerFormat</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>A hint to the client to identify how the bearer token is formatted.<br />
<br />
Bearer tokens are usually generated by an authorization server, so this information is primarily for documentation purposes.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>scheme</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The name of the HTTP Authorization scheme.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr><tr><td style="padding-left: 0;"><i>See</i>:</td><td style="padding-left: 0;"><a href="https://tools.ietf.org/html/rfc7235#section-5.1">RFC7235</a></td></tr></tbody></table></dd>
  <dt><strong>openIdConnectUrl</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>OpenId Connect URL to discover OAuth2 configuration values. This MUST be in the form of a URL.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
</dl>

#### Reference
---
- [Security Scheme Object](https://spec.openapis.org/oas/v3.1.1.html#security-scheme-object-0)

### [Server](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Server.php)

An object representing a server.

#### Allowed in
---
<a href="#openapi">OpenApi</a>, <a href="#pathitem">PathItem</a>, <a href="#operation">Operation</a>, <a href="#get">Get</a>, <a href="#post">Post</a>, <a href="#put">Put</a>, <a href="#delete">Delete</a>, <a href="#patch">Patch</a>, <a href="#head">Head</a>, <a href="#options">Options</a>, <a href="#trace">Trace</a>, <a href="#link">Link</a>

#### Nested elements
---
<a href="#servervariable">ServerVariable</a>, <a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>url</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>An URL to the target host.<br />
<br />
This URL supports Server Variables and may be relative,<br />
to indicate that the host location is relative to the location where the OpenAPI document is being served.<br />
Variable substitutions will be made when a variable is named in {brackets}.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>yes</b></td></tr></tbody></table></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>An optional string describing the host designated by the URL.<br />
<br />
CommonMark syntax may be used for rich text representation.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
</dl>

#### Reference
---
- [Server Object](https://spec.openapis.org/oas/v3.1.1.html#server-object)

### [ServerVariable](https://github.com/zircote/swagger-php/tree/master/src/Annotations/ServerVariable.php)

An object representing a server variable for server URL template substitution.

#### Allowed in
---
<a href="#server">Server</a>

#### Nested elements
---
<a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>serverVariable</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The key into Server->variables array.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>enum</strong> : <span style="font-family: monospace;">array&lt;string|int|float|bool|\UnitEnum&gt;|class-string</span></dt>
  <dd><p>An enumeration of values to be used if the substitution options are from a limited set.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>default</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The default value to use for substitution, and to send, if an alternate value is not supplied.<br />
<br />
Unlike the Schema Object's default, this value must be provided by the consumer.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>yes</b></td></tr></tbody></table></dd>
  <dt><strong>variables</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>A map between a variable name and its value.<br />
<br />
The value is used for substitution in the server's URL template.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>An optional description for the server variable.<br />
<br />
CommonMark syntax MAY be used for rich text representation.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
</dl>

#### Reference
---
- [Server Variable Object](https://spec.openapis.org/oas/v3.1.1.html#server-variable-object)

### [Tag](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Tag.php)



#### Allowed in
---
<a href="#openapi">OpenApi</a>

#### Nested elements
---
<a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>name</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The name of the tag.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>yes</b></td></tr></tbody></table></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>A short description for the tag. GFM syntax can be used for rich text representation.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
</dl>

#### Reference
---
- [Tag Object](https://spec.openapis.org/oas/v3.1.1.html#tag-object)

### [Trace](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Trace.php)



#### Allowed in
---
<a href="#pathitem">PathItem</a>

#### Nested elements
---
<a href="#parameter">Parameter</a>, <a href="#pathparameter">PathParameter</a>, <a href="#response">Response</a>, <a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#server">Server</a>, <a href="#requestbody">RequestBody</a>, <a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>method</strong></dt>
  <dd><p>No details available.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
</dl>

### [Webhook](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Webhook.php)

Acts like a <code>PathItem</code> with the main difference being that it requires <code>webhook</code> instead of <code>path</code>.

#### Allowed in
---
<a href="#openapi">OpenApi</a>

#### Nested elements
---
<a href="#get">Get</a>, <a href="#post">Post</a>, <a href="#put">Put</a>, <a href="#delete">Delete</a>, <a href="#patch">Patch</a>, <a href="#trace">Trace</a>, <a href="#head">Head</a>, <a href="#options">Options</a>, <a href="#parameter">Parameter</a>, <a href="#pathparameter">PathParameter</a>, <a href="#server">Server</a>, <a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>webhook</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>Key for the webhooks map.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>yes</b></td></tr></tbody></table></dd>
</dl>

### [Xml](https://github.com/zircote/swagger-php/tree/master/src/Annotations/Xml.php)



#### Allowed in
---
<a href="#additionalproperties">AdditionalProperties</a>, <a href="#schema">Schema</a>, <a href="#property">Property</a>, <a href="#schema">Schema</a>, <a href="#items">Items</a>, <a href="#xmlcontent">XmlContent</a>

#### Nested elements
---
<a href="#attachable">Attachable</a>

#### Properties
---
<dl>
  <dt><strong>name</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>Replaces the name of the element/attribute used for the described schema property.<br />
<br />
When defined within the Items Object (items), it will affect the name of the individual XML elements within the list.<br />
When defined alongside type being array (outside the items), it will affect the wrapping element<br />
and only if wrapped is <code>true</code>.<br />
<br />
If wrapped is <code>false</code>, it will be ignored.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>namespace</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The URL of the namespace definition. Value SHOULD be in the form of a URL.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>prefix</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>The prefix to be used for the name.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>attribute</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>Declares whether the property definition translates to an attribute instead of an element.<br />
<br />
Default value is <code>false</code>.</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
  <dt><strong>wrapped</strong> : <span style="font-family: monospace;">bool</span></dt>
  <dd><p>MAY be used only for an array definition.<br />
<br />
Signifies whether the array is wrapped (for example  <code>&lt;books>&lt;book/>&lt;book/>&lt;/books></code>)<br />
or unwrapped (<code>&lt;book/>&lt;book/></code>).<br />
<br />
Default value is false. The definition takes effect only when defined alongside type being array (outside the items).</p><table class="table-plain"><tbody><tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>no</b></td></tr></tbody></table></dd>
</dl>

#### Reference
---
- [XML Object](https://spec.openapis.org/oas/v3.1.1.html#xml-object)

### [XmlContent](https://github.com/zircote/swagger-php/tree/master/src/Annotations/XmlContent.php)

Shorthand for a xml response.

Use as <code>@OA\Schema</code> inside a <code>Response</code> and <code>MediaType</code>-><code>'application/xml'</code> will be generated.

#### Nested elements
---
<a href="#discriminator">Discriminator</a>, <a href="#items">Items</a>, <a href="#property">Property</a>, <a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#xml">Xml</a>, <a href="#additionalproperties">AdditionalProperties</a>, <a href="#examples">Examples</a>, <a href="#attachable">Attachable</a>

