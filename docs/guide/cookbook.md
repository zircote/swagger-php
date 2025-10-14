# Cookbook

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
</codeblock>

## Adding examples to `@OA\Response`

<codeblock id="response-examples">
  <template v-slot:at>

<<< @/snippets/guide/cookbook/response_examples_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/cookbook/response_examples_an.php

  </template>
</codeblock>

## External documentation
OpenApi allows a single reference to external documentation. This is a part of the top level `@OA\OpenApi`.

<codeblock id="external-doc">
  <template v-slot:at>

<<< @/snippets/guide/cookbook/external_documentation_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/cookbook/external_documentation_an.php

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
</codeblock>


This will resolve into this YAML
```yaml
openapi: 3.0.0
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
</codeblock>

To declare an endpoint as secure and define what security schemes are available to authenticate a client it needs to be
added to the operation, for example:

<codeblock id="secure-endpoint">
  <template v-slot:at>

<<< @/snippets/guide/cookbook/secure_endpoint_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/cookbook/secure_endpoint_an.php

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
</codeblock>
:::

## File upload with headers

<codeblock id="file-upload-with-headers">
  <template v-slot:at>

<<< @/snippets/guide/cookbook/file_upload_with_headers_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/cookbook/file_upload_with_headers_an.php

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
</codeblock>

## Default security scheme for all endpoints
Unless specified each endpoint needs to declare what security schemes it supports. However, there is a way
to also configure security schemes globally for the whole API.

This is done on the `@OA\OpenApi` annotation:

<codeblock id="minimal">
  <template v-slot:at>

<<< @/snippets/guide/cookbook/default_security_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/cookbook/default_security_an.php

  </template>
</codeblock>

## Nested objects
Complex, nested data structures are defined by nesting `@OA\Property` annotations inside others (with `type="object"`).

<codeblock id="minimal">
  <template v-slot:at>

<<< @/snippets/guide/cookbook/nested_objects_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/cookbook/nested_objects_an.php

  </template>
</codeblock>

## Documenting union type response data using `oneOf`
A response with either a single or a list of `QualificationHolder`'s.

<codeblock id="oneof-example">
  <template v-slot:at>

<<< @/snippets/guide/cookbook/oneof_example_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/cookbook/oneof_example_an.php

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
</codeblock>

::: tip `response` parameter is always required
Even if referencing a shared response definition, the `response` parameter is still required.
:::

## mediaType="*/*"
Using `*/*` as `mediaType` is not possible using annotations.

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

## Warning about `Multiple response with same response="200"`
There are two scenarios where this can happen
1. A single endpoint contains two responses with the same `response` value.
2. There are multiple global response declared, again more than one with the same `response` value.

## Callbacks
The API does include basic support for callbacks. However, this needs to be set up mostly manually.

**Example**

<tabs options="{ useUrlFragment: false }">
  <tab id="at" name="Attributes">

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

  </tab>
  <tab id="an" name="Annotations">

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

  </tab>
</tabs>

## (Mostly) virtual models
Typically, a model is annotated by adding a `@OA\Schema` annotation to the class and then individual `@OA\Property` annotations
to the individually declared class properties.

It is possible, however, to nest `@\Property` annotations inside a schema even without properties. In fact, all that is needed
is a code anchor - e.g. an empty class.

<codeblock id="virtual-model">
  <template v-slot:at>

<<< @/snippets/guide/cookbook/virtual_model_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/cookbook/virtual_model_an.php

  </template>
</codeblock>


## Using class name as type instead of references
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

This works, but is not very convenient.

First, when using custom schema names (`schema: 'user'`), this needs to be taken into account everywhere.
Secondly, having to write `ref: '#/components/schemas/user'` is tedious and error-prone.

Using attributes all this changes as we can take advantage of PHP itself by referring to a schema by its (fully qualified)
class name.

With the same `User` schema as before, the `Book::author` property could be written in a few different ways

<tabs options="{ useUrlFragment: false }">
  <tab id="at" name="Attributes">

```php
    #[OAT\Property()]
    public User author;
```

**or**

```php
    /**
     * @var User
     */
    #[OAT\Property()]
    public author;
```

**or**

```php
    #[OA\Property(type: User::class)]
    public author;
```

  </tab>

  <tab id="an" name="Annotations">

```php
    /** @OA\Property() */
    public User author;
```

**or**

```php
    /**
     * @var User
     * @OA\Property()
     */
    public author;
```

  </tab>
</tabs>

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

PHP allows to have query parameters multiple times in the url and will combine the values to an array if the parameter
name uses trailing `[]`. In fact, it is possible to create nested arrays too by using more than one pair of `[]`.

In terms of OpenAPI, the parameters can be considered a single parameter with a list of values.

<codeblock id="multi-value-query-parameter">
  <template v-slot:at>

<<< @/snippets/guide/cookbook/multi_value_query_parameter_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/cookbook/multi_value_query_parameter_an.php

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

`swagger-ui` will show  a form that allows to add/remove items (`integer`  values in this case) to/from a list
and post those values as something like ```?things[]=1&things[]=2&things[]=0```

## Custom response classes

Even with using refs there is a bit of overhead in sharing responses. One way around that is to write
your own response classes.
The beauty is that in your custom `__construct()` method you can prefill as much as you need.

Best of all, this works for both annotations and attributes.

Example:

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
If you are only interested in annotations you canleave out the attribute setup line (`#[\Attribute...`) for `BadRequest`.

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
