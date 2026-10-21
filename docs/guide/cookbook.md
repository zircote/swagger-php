# Cookbook

## Return JSON from an endpoint

For `application/json` content, `OA\MediaType\Json` takes the schema directly, without a nested `OA\Schema`.
`OA\MediaType\Xml` is the same for XML.

<codeblock id="json-response">
  <template v-slot:spec>

<<< @/snippets/guide/cookbook/json_response_spec.php

  </template>
  <template v-slot:at>

<<< @/snippets/guide/cookbook/json_response_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/cookbook/json_response_an.php

  </template>
</codeblock>

See [`OA\MediaType`](/guide/shortcuts#oa-mediatype-json-and-xml).

## Document a list of objects

A property holding a list takes `OA\Schema\Items`, with the schema of one item as its arguments.

<codeblock id="list-of-objects">
  <template v-slot:spec>

<<< @/snippets/guide/cookbook/list_of_objects_spec.php

  </template>
  <template v-slot:at>

<<< @/snippets/guide/cookbook/list_of_objects_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/cookbook/list_of_objects_an.php

  </template>
</codeblock>

See [`OA\Schema` subclasses](/guide/shortcuts#oa-schema-subclasses).

## Describe an object without a PHP class

Where a controller returns an array rather than an object of a class, `properties` can be keyed by property name.
Classic has no short form for this.

<codeblock id="object-without-class">
  <template v-slot:spec>

<<< @/snippets/guide/cookbook/object_without_class_spec.php

  </template>
  <template v-slot:at>

<<< @/snippets/guide/cookbook/object_without_class_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/cookbook/object_without_class_an.php

  </template>
</codeblock>

A named schema works the same way, with an empty class as the anchor. An `OA\Schema` as the value nests an object:

<codeblock id="named-object">
  <template v-slot:spec>

<<< @/snippets/guide/cookbook/named_object_spec.php

  </template>
  <template v-slot:at>

<<< @/snippets/guide/cookbook/named_object_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/cookbook/named_object_an.php

  </template>
</codeblock>

See [Keyed `properties`](/guide/shortcuts#keyed-properties).

## Add constraints to a class property

A class property needs only an `OA\Schema` with the constraints. The name and the type come from the declaration.
Spec also lists a typed property without a default as `required`.

<codeblock id="property-constraints">
  <template v-slot:spec>

<<< @/snippets/guide/cookbook/property_constraints_spec.php

  </template>
  <template v-slot:at>

<<< @/snippets/guide/cookbook/property_constraints_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/cookbook/property_constraints_an.php

  </template>
</codeblock>

An explicit `OA\Property` is needed only where the document uses a different name, such as `full_name` for `$name`, or
on a getter. See [`OA\Property`](/guide/shortcuts#implied-oa-property).

## Document path, query and header parameters

`OA\Parameter\Path`, `Query`, `Header` and `Cookie` set `in`, and a path parameter is always required.

<codeblock id="parameters">
  <template v-slot:spec>

<<< @/snippets/guide/cookbook/parameters_spec.php

  </template>
  <template v-slot:at>

<<< @/snippets/guide/cookbook/parameters_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/cookbook/parameters_an.php

  </template>
</codeblock>

See [`OA\Parameter`](/guide/shortcuts#oa-parameter).

## Document several operations on one path

Each controller method gets the attribute for its HTTP method, `OA\Operation\Get`, `OA\Operation\Post` and so on.

<codeblock id="operations-per-method">
  <template v-slot:spec>

<<< @/snippets/guide/cookbook/operations_per_method_spec.php

  </template>
  <template v-slot:at>

<<< @/snippets/guide/cookbook/operations_per_method_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/cookbook/operations_per_method_an.php

  </template>
</codeblock>

See [`OA\Operation\*`](/guide/shortcuts#oa-operation).

## `x-tagGroups`
OpenApi has the concept of grouping endpoints using tags. On top of that, some tools
([redocly](https://redoc.ly/docs/api-reference-docs/specification-extensions/x-tag-groups/), for example)
support further grouping via the vendor extension `x-tagGroups`.

<codeblock id="x-tag-groups">
  <template v-slot:at>

<<< @/snippets/guide/cookbook/x_tag_groups_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/cookbook/x_tag_groups_an.php

  </template>
  <template v-slot:spec>

<<< @/snippets/guide/cookbook/x_tag_groups_spec.php

  </template>
</codeblock>

## Adding examples to `@OA\Response`

<codeblock id="response-examples">
  <template v-slot:at>

<<< @/snippets/guide/cookbook/response_examples_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/cookbook/response_examples_an.php

  </template>
  <template v-slot:spec>

<<< @/snippets/guide/cookbook/response_examples_spec.php

  </template>
</codeblock>

## Adding examples to `@OA\Schema`

A schema's `examples` is the JSON Schema keyword rather than a map of Example Objects, so it
takes a list of values. `@OA\Property`, `@OA\Items` and `@OA\AdditionalProperties` are schemas
too and take the same list.

<codeblock id="schema-examples">
  <template v-slot:at>

<<< @/snippets/guide/cookbook/schema_examples_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/cookbook/schema_examples_an.php

  </template>
  <template v-slot:spec>

<<< @/snippets/guide/cookbook/schema_examples_spec.php

  </template>
</codeblock>

A nested `@OA\Examples` is accepted here and contributes its `value`. The rest of an Example
Object (the key, `summary`, `description`, `externalValue`) has nowhere to go in a list and is
dropped. A media type, parameter or header can carry those instead.

::: info Mode difference
The keyword arrived in OpenAPI 3.1. Generating 3.0 from the same source drops the list in
`classic` and carries the first value across as `example` in `spec`. Both warn.
:::

## External documentation
OpenApi allows a single reference to external documentation. This is a part of the top level `@OA\OpenApi`.

<codeblock id="external-doc">
  <template v-slot:at>

<<< @/snippets/guide/cookbook/external_documentation_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/cookbook/external_documentation_an.php

  </template>
  <template v-slot:spec>

<<< @/snippets/guide/cookbook/external_documentation_spec.php

  </template>
</codeblock>

::: tip
If no `@OA\OpenApi` is configured, `swagger-php` will create one automatically.

That means the above example would also work with just the `OA\ExternalDocumentation` annotation.
```php
/**
 * @OA\ExternalDocumentation(
 *   description="More documentation here...",
 *   url="https://example.com/externaldoc1/"
 * )
 */
```

:::

## Properties with union types
Sometimes properties or even lists (arrays) may contain data of different types. This can be expressed using `oneOf`.

<codeblock id="props-with-union-types">
  <template v-slot:at>

<<< @/snippets/guide/cookbook/properties_with_union_types_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/cookbook/properties_with_union_types_an.php

  </template>
  <template v-slot:spec>

<<< @/snippets/guide/cookbook/properties_with_union_types_spec.php

  </template>
</codeblock>


This will resolve into this YAML
```yaml
openapi: 3.1.0
components:
  schemas:
    StringList:
      properties:
        value:
          type: array
          items:
            anyOf:
              -
                type: string
      type: object
    String:
      properties:
        value:
          type: string
      type: object
    Object:
      properties:
        value:
          type: object
      type: object
    mixedList:
      properties:
        fields:
          type: array
          items:
            oneOf:
              -
                $ref: '#/components/schemas/StringList'
              -
                $ref: '#/components/schemas/String'
              -
                $ref: '#/components/schemas/Object'
      type: object
```

## Referencing a security scheme
An API might have zero or more security schemes. These are defined at the top level and vary from simple to complex:

<codeblock id="security-schemas">
  <template v-slot:at>

<<< @/snippets/guide/cookbook/security_schemas_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/cookbook/security_schemas_an.php

  </template>
  <template v-slot:spec>

<<< @/snippets/guide/cookbook/security_schemas_spec.php

  </template>
</codeblock>

To declare an endpoint as secure and define what security schemes are available to authenticate a client, it needs to be
added to the operation, for example:

<codeblock id="secure-endpoint">
  <template v-slot:at>

<<< @/snippets/guide/cookbook/secure_endpoint_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/cookbook/secure_endpoint_an.php

  </template>
  <template v-slot:spec>

<<< @/snippets/guide/cookbook/secure_endpoint_spec.php

  </template>
</codeblock>

::: tip Endpoints can support multiple security schemes and have custom options too:
<codeblock id="security-schema-tips">
  <template v-slot:at>

<<< @/snippets/guide/cookbook/security_schema_tips_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/cookbook/security_schema_tips_an.php

  </template>
  <template v-slot:spec>

<<< @/snippets/guide/cookbook/security_schema_tips_spec.php

  </template>
</codeblock>
:::

See [`OA\Security\Requirement`](/guide/shortcuts#oa-security-requirement) and
[`OA\Security\Scheme\*`](/guide/shortcuts#oa-security-scheme-and-oa-flow).

## File upload with headers

<codeblock id="file-upload-with-headers">
  <template v-slot:at>

<<< @/snippets/guide/cookbook/file_upload_with_headers_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/cookbook/file_upload_with_headers_an.php

  </template>
  <template v-slot:spec>

<<< @/snippets/guide/cookbook/file_upload_with_headers_spec.php

  </template>
</codeblock>

## Set the XML root name

The `OA\Xml` annotation may be used to set the XML root element for a given `@OA\XmlContent` response body

<codeblock id="set-xml-root-name">
  <template v-slot:at>

<<< @/snippets/guide/cookbook/set_xml_root_name_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/cookbook/set_xml_root_name_an.php

  </template>
  <template v-slot:spec>

<<< @/snippets/guide/cookbook/set_xml_root_name_spec.php

  </template>
</codeblock>

## upload multipart/form-data
Form posts are `@OA\Post` requests with a `multipart/form-data` `@OA\RequestBody`. The relevant bit looks something like this

<codeblock id="upload-multipart-form-data">
  <template v-slot:at>

<<< @/snippets/guide/cookbook/uploading_multipart_formdata_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/cookbook/uploading_multipart_formdata_an.php

  </template>
  <template v-slot:spec>

<<< @/snippets/guide/cookbook/uploading_multipart_formdata_spec.php

  </template>
</codeblock>

## Default security scheme for all endpoints
Unless specified, each endpoint needs to declare what security schemes it supports. However, there is a way
to also configure security schemes globally for the whole API.

This is done on the `@OA\OpenApi` annotation:

<codeblock id="minimal">
  <template v-slot:at>

<<< @/snippets/guide/cookbook/default_security_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/cookbook/default_security_an.php

  </template>
  <template v-slot:spec>

<<< @/snippets/guide/cookbook/default_security_spec.php

  </template>
</codeblock>

See [`OA\Security\Requirement`](/guide/shortcuts#oa-security-requirement).

## Documenting union type response data using `oneOf`
A response with either a single or a list of `QualificationHolder`'s.

<codeblock id="oneof-example">
  <template v-slot:at>

<<< @/snippets/guide/cookbook/oneof_example_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/cookbook/oneof_example_an.php

  </template>
  <template v-slot:spec>

<<< @/snippets/guide/cookbook/oneof_example_spec.php

  </template>
</codeblock>

## Reusing responses
Global responses are found under `/components/responses` and can be referenced/shared just like schema definitions (models)

<codeblock id="reusing-response">
  <template v-slot:at>

<<< @/snippets/guide/cookbook/reusing_response_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/cookbook/reusing_response_an.php

  </template>
  <template v-slot:spec>

<<< @/snippets/guide/cookbook/reusing_response_spec.php

  </template>
</codeblock>

::: tip `response` parameter is always required
Even if referencing a shared response definition, the `response` parameter is still required.
:::

## mediaType="*/*" <Badge type="warning" text="deprecated" />
Using `*/*` as `mediaType` is not possible using annotations. Attributes do not have this problem.

**Example:**
```php
/**
 * @OA\MediaType(
 *     mediaType="*/*",
 *     @OA\Schema(type="string",format="binary")
 * )
 */
```

The doctrine annotations library used for parsing annotations does not handle this and will interpret the `*/` bit as the end of the comment.

Using just `*` or `application/octet-stream` might be usable workarounds.

## Callbacks
The API does include basic support for callbacks. However, this needs to be set up mostly manually.

**Example**

<codeblock id="callbacks">
  <template v-slot:spec>

```php
#[OA\Operation\Get(
    // ...
    callbacks: [
        'onChange' => [
            '{$request.query.callbackUrl}' => [
                'post' => [
                    'requestBody' => new OA\RequestBody(
                        description: 'subscription payload',
                        content: [
                            new OA\MediaType\Json(
                                schema: new OA\Schema(
                                    properties: [
                                        new OA\Property(
                                            property: 'timestamp',
                                            schema: new OA\Schema(
                                                type: 'string',
                                                format: 'date-time',
                                                description: 'time of change',
                                            ),
                                        ),
                                    ],
                                ),
                            ),
                        ],
                    ),
                ],
            ],
        ],
    ],
    // ...
)]
```

  </template>
  <template v-slot:at>

```php
#[OA\Get(
    // ...
    callbacks: [
        'onChange' => [
            '{$request.query.callbackUrl}' => [
                'post' => [
                    'requestBody' => new OA\RequestBody(
                        description: 'subscription payload',
                        content: [
                            new OA\MediaType(
                                mediaType: 'application/json',
                                schema: new OA\Schema(
                                    properties: [
                                        new OA\Property(
                                            property: 'timestamp',
                                            type: 'string',
                                            format: 'date-time',
                                            description: 'time of change'
                                        ),
                                    ],
                                ),
                            ),
                        ],
                    ),
                ],
            ],
        ],
    ],
    // ...
)]
```

  </template>
  <template v-slot:an>

```php
/**
 * @OA\Get(
 *     ...
 *     callbacks={
 *         "onChange"={
 *              "{$request.query.callbackUrl}"={
 *                  "post": {
 *                      "requestBody": @OA\RequestBody(
 *                          description="subscription payload",
 *                          @OA\MediaType(mediaType="application/json", @OA\Schema(
 *                              @OA\Property(property="timestamp", type="string", format="date-time", description="time of change")
 *                          ))
 *                      )
 *                  },
 *                  "responses": {
 *                      "202": {
 *                          "description": "Your server implementation should return this HTTP status code if the data was received successfully"
 *                      }
 *                  }
 *              }
 *         }
 *     }
 *     ...
 * )
 */
```

  </template>
</codeblock>

## Using class name as a type instead of references
Typically, when referencing schemas this is done using `$ref`'s

```php
#[OAT\Schema(schema: 'user')]
class User
{
}

#[OAT\Schema()]
class Book
{
    /**
     * @var User
     */
    #[OAT\Property(ref: '#/components/schemas/user')]
    public $author;
}
```

This works but is not very convenient.

First, when using custom schema names (`schema: 'user'`), this needs to be taken into account everywhere.
Secondly, having to write `ref: '#/components/schemas/user'` is tedious and error-prone.

Using attributes, all this changes as we can take advantage of PHP itself by referring to a schema by its (fully qualified)
class name.

With the same `User` schema as before, the `Book::author` property could be written in a few different ways

<codeblock id="class-name-as-type">
  <template v-slot:spec>

```php
    #[OA\Property]
    public User $author;
```

**or**

```php
    /**
     * @var User
     */
    #[OA\Property]
    public $author;
```

**or**

```php
    #[OA\Schema(ref: User::class)]
    public $author;
```

  </template>
  <template v-slot:at>

```php
    #[OAT\Property()]
    public User $author;
```

**or**

```php
    /**
     * @var User
     */
    #[OAT\Property()]
    public $author;
```

**or**

```php
    #[OAT\Property(type: User::class)]
    public $author;
```

  </template>
  <template v-slot:an>

```php
    /** @OA\Property() */
    public User $author;
```

**or**

```php
    /**
     * @var User
     * @OA\Property()
     */
    public $author;
```

  </template>
</codeblock>

## Enums
As of PHP 8.1 there is native support for `enum`'s.

`swagger-php` supports enums in much the same way as class names can be used to reference schemas.

**Example**

<codeblock id="enums">
  <template v-slot:at>

<<< @/snippets/guide/cookbook/enums_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/cookbook/enums_an.php

  </template>
  <template v-slot:spec>

<<< @/snippets/guide/cookbook/enums_spec.php

  </template>
</codeblock>

However, in this case the schema generated for `State` will be an enum:

```yaml
components:
  schemas:
    PullRequest:
      properties:
        state:
          $ref: '#/components/schemas/State'
      type: object
    State:
      type: string
      enum:
        - OPEN
        - MERGED
        - DECLINED
```

## Multi value query parameter: `&q[]=1&q[]=1`

PHP allows having query parameters multiple times in the url and will combine the values to an array if the parameter
name uses trailing `[]`. In fact, it is possible to create nested arrays too by using more than one pair of `[]`.

In terms of OpenAPI, the parameters can be considered a single parameter with a list of values.

<codeblock id="multi-value-query-parameter">
  <template v-slot:at>

<<< @/snippets/guide/cookbook/multi_value_query_parameter_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/cookbook/multi_value_query_parameter_an.php

  </template>
  <template v-slot:spec>

<<< @/snippets/guide/cookbook/multi_value_query_parameter_spec.php

  </template>
</codeblock>


The corresponding bit of the spec will look like this:

```yaml
      parameters:
        -
          name: 'things[]'
          in: query
          description: 'A list of things.'
          required: false
          schema:
            type: array
            items:
              type: integer
```

`swagger-ui` will show a form that allows to add/remove items (`integer`  values in this case) to/from a list
and post those values as something like ```?things[]=1&things[]=2&things[]=0```

See [`OA\Parameter\*`](/guide/shortcuts#oa-parameter) and [`OA\Schema` subclasses](/guide/shortcuts#oa-schema-subclasses).

## Custom response classes

Even with using refs, there is a bit of overhead in sharing responses. One way around that is to write
your own response classes.
The beauty is that in your custom `__construct()` method you can prefill as much as you need.

Example:

```php
use OpenApi\Spec as OA;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class BadRequest extends OA\Response
{
    public function __construct()
    {
        parent::__construct(response: 400, description: 'Bad request');
    }
}

class Controller
{
    #[OA\Operation\Get(path: '/foo', responses: [new BadRequest()])]
    public function get()
    {
    }

    #[OA\Operation\Post(path: '/foo')]
    #[BadRequest]
    public function post()
    {
    }
}
```

### Classic attributes and annotations <Badge type="warning" text="deprecated" />

```php
use OpenApi\Attributes as OA;

/**
 * @Annotation
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class BadRequest extends OA\Response
{
    public function __construct()
    {
        parent::__construct(response: 400, description: 'Bad request');
    }
}

class Controller
{

    #[OA\Get(path: '/foo', responses: [new BadRequest()])]
    public function get()
    {
    }

    #[OA\Post(path: '/foo')]
    #[BadRequest]
    public function post()
    {
    }

    /**
     * @OA\Delete(
     *     path="/foo",
     *     @BadRequest()
     * )
     */
    public function delete()
    {
    }
}
```

::: tip Annotations only?
If you are only interested in annotations, you can leave out the attribute setup line (`#[\Attribute...`) for `BadRequest`.

Furthermore, your custom annotations should extend from the `OpenApi\Annotations` namespace.
:::

## Annotating class constants

<codeblock id="class-constants">
  <template v-slot:at>

<<< @/snippets/guide/cookbook/class_constants_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/cookbook/class_constants_an.php

  </template>
  <template v-slot:spec>

<<< @/snippets/guide/cookbook/class_constants_spec.php

  </template>
</codeblock>

The `const` property is supported in OpenApi 3.1.0.
```yaml
components:
  schemas:
    Airport:
        properties:
          kind:
            type: string
            const: Airport
```
For 3.0.0 this is serialized into a single value `enum`.
```yaml
components:
  schemas:
    Airport:
        properties:
          kind:
            type: string
            enum:
              - Airport
```
