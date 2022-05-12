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
  <dt><strong>ref</strong> : <span style="font-family: monospace;">object|string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>schema</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>title</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>required</strong> : <span style="font-family: monospace;">string[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>properties</strong> : <span style="font-family: monospace;">Property[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>type</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>format</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>items</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Items|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>collectionFormat</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>default</strong></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>maximum</strong> : <span style="font-family: monospace;">int|float</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>exclusiveMaximum</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>minimum</strong> : <span style="font-family: monospace;">int|float</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>exclusiveMinimum</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>maxLength</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>minLength</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>maxItems</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>minItems</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>uniqueItems</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>pattern</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>enum</strong> : <span style="font-family: monospace;">string[]|int[]|float[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>discriminator</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Discriminator|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>readOnly</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>writeOnly</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>xml</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Xml|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>externalDocs</strong> : <span style="font-family: monospace;">OpenApi\Attributes\ExternalDocumentation|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>example</strong></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>nullable</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>allOf</strong> : <span style="font-family: monospace;">Schema[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>anyOf</strong> : <span style="font-family: monospace;">Schema[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>oneOf</strong> : <span style="font-family: monospace;">Schema[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
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
  <dt><strong>schemas</strong> : <span style="font-family: monospace;">Schema[]|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>responses</strong> : <span style="font-family: monospace;">Response[]|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>parameters</strong> : <span style="font-family: monospace;">Parameter[]|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>requestBodies</strong> : <span style="font-family: monospace;">RequestBody[]|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>examples</strong> : <span style="font-family: monospace;">Examples[]|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>headers</strong> : <span style="font-family: monospace;">Header[]|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>securitySchemes</strong> : <span style="font-family: monospace;">SecurityScheme[]|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>links</strong> : <span style="font-family: monospace;">Link[]|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>callbacks</strong> : <span style="font-family: monospace;">callable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
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
  <dd><p>No details available.</p></dd>
  <dt><strong>url</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>email</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
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
  <dd><p>No details available.</p></dd>
  <dt><strong>operationId</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>summary</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>security</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>servers</strong> : <span style="font-family: monospace;">Server[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>requestBody</strong> : <span style="font-family: monospace;">OpenApi\Attributes\RequestBody|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>tags</strong> : <span style="font-family: monospace;">string[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>parameters</strong> : <span style="font-family: monospace;">Parameter[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>responses</strong> : <span style="font-family: monospace;">Response[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>callbacks</strong> : <span style="font-family: monospace;">callable[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>externalDocs</strong> : <span style="font-family: monospace;">OpenApi\Attributes\ExternalDocumentation|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
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
  <dd><p>No details available.</p></dd>
  <dt><strong>mapping</strong> : <span style="font-family: monospace;">string[]|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
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
  <dd><p>No details available.</p></dd>
  <dt><strong>summary</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>value</strong> : <span style="font-family: monospace;">array|string|int|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>externalValue</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">object|string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
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
  <dd><p>No details available.</p></dd>
  <dt><strong>url</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
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
  <dd><p>No details available.</p></dd>
  <dt><strong>tokenUrl</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>refreshUrl</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>flow</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>scopes</strong> : <span style="font-family: monospace;">array|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
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
  <dd><p>No details available.</p></dd>
  <dt><strong>operationId</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>summary</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>security</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>servers</strong> : <span style="font-family: monospace;">Server[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>requestBody</strong> : <span style="font-family: monospace;">OpenApi\Attributes\RequestBody|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>tags</strong> : <span style="font-family: monospace;">string[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>parameters</strong> : <span style="font-family: monospace;">Parameter[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>responses</strong> : <span style="font-family: monospace;">Response[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>callbacks</strong> : <span style="font-family: monospace;">callable[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>externalDocs</strong> : <span style="font-family: monospace;">OpenApi\Attributes\ExternalDocumentation|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
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
  <dd><p>No details available.</p></dd>
  <dt><strong>operationId</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>summary</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>security</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>servers</strong> : <span style="font-family: monospace;">Server[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>requestBody</strong> : <span style="font-family: monospace;">OpenApi\Attributes\RequestBody|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>tags</strong> : <span style="font-family: monospace;">string[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>parameters</strong> : <span style="font-family: monospace;">Parameter[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>responses</strong> : <span style="font-family: monospace;">Response[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>callbacks</strong> : <span style="font-family: monospace;">callable[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>externalDocs</strong> : <span style="font-family: monospace;">OpenApi\Attributes\ExternalDocumentation|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
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
  <dt><strong>ref</strong> : <span style="font-family: monospace;">object|string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>header</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>required</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>schema</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Schema|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>allowEmptyValue</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
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
  <dd><p>No details available.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>title</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>termsOfService</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>contact</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Contact|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>license</strong> : <span style="font-family: monospace;">OpenApi\Attributes\License|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
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
  <dt><strong>ref</strong> : <span style="font-family: monospace;">object|string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>schema</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>title</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>required</strong> : <span style="font-family: monospace;">string[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>properties</strong> : <span style="font-family: monospace;">Property[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>type</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>format</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>items</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Items|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>collectionFormat</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>default</strong></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>maximum</strong> : <span style="font-family: monospace;">int|float</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>exclusiveMaximum</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>minimum</strong> : <span style="font-family: monospace;">int|float</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>exclusiveMinimum</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>maxLength</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>minLength</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>maxItems</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>minItems</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>uniqueItems</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>pattern</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>enum</strong> : <span style="font-family: monospace;">string[]|int[]|float[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>discriminator</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Discriminator|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>readOnly</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>writeOnly</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>xml</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Xml|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>externalDocs</strong> : <span style="font-family: monospace;">OpenApi\Attributes\ExternalDocumentation|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>example</strong></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>nullable</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>allOf</strong> : <span style="font-family: monospace;">Schema[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>anyOf</strong> : <span style="font-family: monospace;">Schema[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>oneOf</strong> : <span style="font-family: monospace;">Schema[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>additionalProperties</strong> : <span style="font-family: monospace;">OpenApi\Attributes\AdditionalProperties|bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
</dl>

## [JsonContent](https://github.com/zircote/swagger-php/tree/master/src/Attributes/JsonContent.php)



#### Nested elements
---
<a href="#discriminator">Discriminator</a>, <a href="#items">Items</a>, <a href="#property">Property</a>, <a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#additionalproperties">AdditionalProperties</a>, <a href="#examples">Examples</a>, <a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>examples</strong> : <span style="font-family: monospace;">array&lt;string,Examples&gt;</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">object|string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>schema</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>title</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>required</strong> : <span style="font-family: monospace;">string[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>properties</strong> : <span style="font-family: monospace;">Property[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>type</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>format</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>items</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Items|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>collectionFormat</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>default</strong></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>maximum</strong> : <span style="font-family: monospace;">int|float</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>exclusiveMaximum</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>minimum</strong> : <span style="font-family: monospace;">int|float</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>exclusiveMinimum</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>maxLength</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>minLength</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>maxItems</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>minItems</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>uniqueItems</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>pattern</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>enum</strong> : <span style="font-family: monospace;">string[]|int[]|float[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>discriminator</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Discriminator|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>readOnly</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>writeOnly</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>xml</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Xml|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>externalDocs</strong> : <span style="font-family: monospace;">OpenApi\Attributes\ExternalDocumentation|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>example</strong></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>nullable</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>allOf</strong> : <span style="font-family: monospace;">Schema[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>anyOf</strong> : <span style="font-family: monospace;">Schema[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>oneOf</strong> : <span style="font-family: monospace;">Schema[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>additionalProperties</strong> : <span style="font-family: monospace;">OpenApi\Attributes\AdditionalProperties|bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
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
  <dd><p>No details available.</p></dd>
  <dt><strong>identifier</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>url</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
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
  <dd><p>No details available.</p></dd>
  <dt><strong>operationRef</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">object|string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>operationId</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>parameters</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>requestBody</strong></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>server</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Server|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
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
  <dd><p>No details available.</p></dd>
  <dt><strong>schema</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Schema|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>example</strong></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>examples</strong> : <span style="font-family: monospace;">array&lt;string,Examples&gt;</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>encoding</strong> : <span style="font-family: monospace;">array&lt;string,mixed&gt;</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
</dl>

## [OpenApi](https://github.com/zircote/swagger-php/tree/master/src/Attributes/OpenApi.php)



#### Nested elements
---
<a href="#info">Info</a>, <a href="#server">Server</a>, <a href="#pathitem">PathItem</a>, <a href="#components">Components</a>, <a href="#tag">Tag</a>, <a href="#externaldocumentation">ExternalDocumentation</a>, <a href="#attachable">Attachable</a>

#### Parameters
---
<dl>
  <dt><strong>openapi</strong> : <span style="font-family: monospace;">string</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>info</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Info|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>servers</strong> : <span style="font-family: monospace;">Server[]|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>security</strong> : <span style="font-family: monospace;">array|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>tags</strong> : <span style="font-family: monospace;">Tag[]|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>externalDocs</strong> : <span style="font-family: monospace;">OpenApi\Attributes\ExternalDocumentation|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>paths</strong> : <span style="font-family: monospace;">PathItem[]|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>components</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Components|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
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
  <dd><p>No details available.</p></dd>
  <dt><strong>operationId</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>summary</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>security</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>servers</strong> : <span style="font-family: monospace;">Server[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>requestBody</strong> : <span style="font-family: monospace;">OpenApi\Attributes\RequestBody|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>tags</strong> : <span style="font-family: monospace;">string[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>parameters</strong> : <span style="font-family: monospace;">Parameter[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>responses</strong> : <span style="font-family: monospace;">Response[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>callbacks</strong> : <span style="font-family: monospace;">callable[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>externalDocs</strong> : <span style="font-family: monospace;">OpenApi\Attributes\ExternalDocumentation|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
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
  <dd><p>No details available.</p></dd>
  <dt><strong>name</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>in</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>required</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>allowEmptyValue</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">object|string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>schema</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Schema|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>example</strong></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>examples</strong> : <span style="font-family: monospace;">array&lt;string,Examples&gt;</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>style</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>explode</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>allowReserved</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>spaceDelimited</strong> : <span style="font-family: monospace;">array|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>pipeDelimited</strong> : <span style="font-family: monospace;">array|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
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
  <dd><p>No details available.</p></dd>
  <dt><strong>operationId</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>summary</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>security</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>servers</strong> : <span style="font-family: monospace;">Server[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>requestBody</strong> : <span style="font-family: monospace;">OpenApi\Attributes\RequestBody|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>tags</strong> : <span style="font-family: monospace;">string[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>parameters</strong> : <span style="font-family: monospace;">Parameter[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>responses</strong> : <span style="font-family: monospace;">Response[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>callbacks</strong> : <span style="font-family: monospace;">callable[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>externalDocs</strong> : <span style="font-family: monospace;">OpenApi\Attributes\ExternalDocumentation|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
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
  <dd><p>No details available.</p></dd>
  <dt><strong>summary</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>servers</strong> : <span style="font-family: monospace;">Server[]|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>parameters</strong> : <span style="font-family: monospace;">Parameter[]|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
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
  <dd><p>No details available.</p></dd>
  <dt><strong>name</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>in</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>required</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>allowEmptyValue</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">object|string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>schema</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Schema|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>example</strong></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>examples</strong> : <span style="font-family: monospace;">array&lt;string,Examples&gt;</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>style</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>explode</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>allowReserved</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>spaceDelimited</strong> : <span style="font-family: monospace;">array|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>pipeDelimited</strong> : <span style="font-family: monospace;">array|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
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
  <dd><p>No details available.</p></dd>
  <dt><strong>operationId</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>summary</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>security</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>servers</strong> : <span style="font-family: monospace;">Server[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>requestBody</strong> : <span style="font-family: monospace;">OpenApi\Attributes\RequestBody|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>tags</strong> : <span style="font-family: monospace;">string[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>parameters</strong> : <span style="font-family: monospace;">Parameter[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>responses</strong> : <span style="font-family: monospace;">Response[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>callbacks</strong> : <span style="font-family: monospace;">callable[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>externalDocs</strong> : <span style="font-family: monospace;">OpenApi\Attributes\ExternalDocumentation|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
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
  <dd><p>No details available.</p></dd>
  <dt><strong>ref</strong> : <span style="font-family: monospace;">object|string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>schema</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>title</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>required</strong> : <span style="font-family: monospace;">string[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>properties</strong> : <span style="font-family: monospace;">Property[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>type</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>format</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>items</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Items|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>collectionFormat</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>default</strong></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>maximum</strong> : <span style="font-family: monospace;">int|float</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>exclusiveMaximum</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>minimum</strong> : <span style="font-family: monospace;">int|float</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>exclusiveMinimum</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>maxLength</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>minLength</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>maxItems</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>minItems</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>uniqueItems</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>pattern</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>enum</strong> : <span style="font-family: monospace;">string[]|int[]|float[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>discriminator</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Discriminator|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>readOnly</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>writeOnly</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>xml</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Xml|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>externalDocs</strong> : <span style="font-family: monospace;">OpenApi\Attributes\ExternalDocumentation|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>example</strong></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>nullable</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>allOf</strong> : <span style="font-family: monospace;">Schema[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>anyOf</strong> : <span style="font-family: monospace;">Schema[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>oneOf</strong> : <span style="font-family: monospace;">Schema[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>additionalProperties</strong> : <span style="font-family: monospace;">OpenApi\Attributes\AdditionalProperties|bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
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
  <dd><p>No details available.</p></dd>
  <dt><strong>operationId</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>summary</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>security</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>servers</strong> : <span style="font-family: monospace;">Server[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>requestBody</strong> : <span style="font-family: monospace;">OpenApi\Attributes\RequestBody|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>tags</strong> : <span style="font-family: monospace;">string[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>parameters</strong> : <span style="font-family: monospace;">Parameter[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>responses</strong> : <span style="font-family: monospace;">Response[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>callbacks</strong> : <span style="font-family: monospace;">callable[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>externalDocs</strong> : <span style="font-family: monospace;">OpenApi\Attributes\ExternalDocumentation|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
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
  <dt><strong>ref</strong> : <span style="font-family: monospace;">object|string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>request</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>required</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>content</strong> : <span style="font-family: monospace;">array&lt;MediaType&gt;|JsonContent|XmlContent|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
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
  <dt><strong>ref</strong> : <span style="font-family: monospace;">object|string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>response</strong> : <span style="font-family: monospace;">string|int|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>headers</strong> : <span style="font-family: monospace;">Header[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>content</strong> : <span style="font-family: monospace;">MediaType|JsonContent|XmlContent|array&lt;MediaType|XmlContent&gt;</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>links</strong> : <span style="font-family: monospace;">Link[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
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
  <dt><strong>ref</strong> : <span style="font-family: monospace;">object|string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>schema</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>title</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>required</strong> : <span style="font-family: monospace;">string[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>properties</strong> : <span style="font-family: monospace;">Property[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>type</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>format</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>items</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Items|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>collectionFormat</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>default</strong></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>maximum</strong> : <span style="font-family: monospace;">int|float</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>exclusiveMaximum</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>minimum</strong> : <span style="font-family: monospace;">int|float</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>exclusiveMinimum</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>maxLength</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>minLength</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>maxItems</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>minItems</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>uniqueItems</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>pattern</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>enum</strong> : <span style="font-family: monospace;">string[]|int[]|float[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>discriminator</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Discriminator|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>readOnly</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>writeOnly</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>xml</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Xml|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>externalDocs</strong> : <span style="font-family: monospace;">OpenApi\Attributes\ExternalDocumentation|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>example</strong></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>nullable</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>allOf</strong> : <span style="font-family: monospace;">Schema[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>anyOf</strong> : <span style="font-family: monospace;">Schema[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>oneOf</strong> : <span style="font-family: monospace;">Schema[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>additionalProperties</strong> : <span style="font-family: monospace;">OpenApi\Attributes\AdditionalProperties|bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>const</strong> : <span style="font-family: monospace;">mixed</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
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
  <dt><strong>ref</strong> : <span style="font-family: monospace;">object|string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>securityScheme</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>type</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>name</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>in</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>bearerFormat</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>scheme</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>openIdConnectUrl</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>flows</strong> : <span style="font-family: monospace;">Flow[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
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
  <dd><p>No details available.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>variables</strong> : <span style="font-family: monospace;">ServerVariable[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
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
  <dd><p>No details available.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>default</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>enum</strong> : <span style="font-family: monospace;">string[]|int[]|float[]|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>variables</strong> : <span style="font-family: monospace;">array|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
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
  <dd><p>No details available.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>externalDocs</strong> : <span style="font-family: monospace;">OpenApi\Attributes\ExternalDocumentation|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
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
  <dd><p>No details available.</p></dd>
  <dt><strong>operationId</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>summary</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>security</strong> : <span style="font-family: monospace;">array</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>servers</strong> : <span style="font-family: monospace;">Server[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>requestBody</strong> : <span style="font-family: monospace;">OpenApi\Attributes\RequestBody|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>tags</strong> : <span style="font-family: monospace;">string[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>parameters</strong> : <span style="font-family: monospace;">Parameter[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>responses</strong> : <span style="font-family: monospace;">Response[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>callbacks</strong> : <span style="font-family: monospace;">callable[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>externalDocs</strong> : <span style="font-family: monospace;">OpenApi\Attributes\ExternalDocumentation|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
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
  <dd><p>No details available.</p></dd>
  <dt><strong>namespace</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>prefix</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attribute</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>wrapped</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
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
  <dt><strong>ref</strong> : <span style="font-family: monospace;">object|string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>schema</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>title</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>description</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>required</strong> : <span style="font-family: monospace;">string[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>properties</strong> : <span style="font-family: monospace;">Property[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>type</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>format</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>items</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Items|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>collectionFormat</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>default</strong></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>maximum</strong> : <span style="font-family: monospace;">int|float</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>exclusiveMaximum</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>minimum</strong> : <span style="font-family: monospace;">int|float</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>exclusiveMinimum</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>maxLength</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>minLength</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>maxItems</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>minItems</strong> : <span style="font-family: monospace;">int|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>uniqueItems</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>pattern</strong> : <span style="font-family: monospace;">string|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>enum</strong> : <span style="font-family: monospace;">string[]|int[]|float[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>discriminator</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Discriminator|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>readOnly</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>writeOnly</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>xml</strong> : <span style="font-family: monospace;">OpenApi\Attributes\Xml|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>externalDocs</strong> : <span style="font-family: monospace;">OpenApi\Attributes\ExternalDocumentation|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>example</strong></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>nullable</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>deprecated</strong> : <span style="font-family: monospace;">bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>allOf</strong> : <span style="font-family: monospace;">Schema[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>anyOf</strong> : <span style="font-family: monospace;">Schema[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>oneOf</strong> : <span style="font-family: monospace;">Schema[]</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>additionalProperties</strong> : <span style="font-family: monospace;">OpenApi\Attributes\AdditionalProperties|bool|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>x</strong> : <span style="font-family: monospace;">array&lt;string,string&gt;|null</span></dt>
  <dd><p>No details available.</p></dd>
  <dt><strong>attachables</strong> : <span style="font-family: monospace;">Attachable[]|null</span></dt>
  <dd><p>No details available.</p></dd>
</dl>

