# Cookbook

## `x-tagGroups`
OpenApi has the concept of grouping endpoints using tags. On top of that, some tools 
([redocly](https://redoc.ly/docs/api-reference-docs/specification-extensions/x-tag-groups/), for example) 
support further grouping via the vendor extension `x-tagGroups`.

```php
/** 
 * @OA\OpenApi(
 *   x={
 *       "tagGroups"=
 *           {{"name"="User Management", "tags"={"Users", "API keys", "Admin"}}
 *       }
 *   }
 * )
 */ 
```

## Adding examples to `@OA\Response`
```php
/*
 * @OA\Response(
 *     response=200,
 *     description="OK",
 *     @OA\JsonContent(
 *         oneOf={
 *             @OA\Schema(ref="#/components/schemas/Result"),
 *             @OA\Schema(type="boolean")
 *         },
 *         @OA\Examples(example="result", value={"success": true}, summary="An result object."),
 *         @OA\Examples(example="bool", value=false, summary="A boolean value."),
 *     )
 * )
 */
```

## External documentation
OpenApi allows a single reference to external documentation. This is a part of the top level `@OA\OpenApi`.

```php
/**
 * @OA\OpenApi(
 *   @OA\ExternalDocumentation(
 *     description="More documentation here...",
 *     url="https://example.com/externaldoc1/"
 *   )
 * )
 */
```

::: tip
If no `@OA\OpenApi` is configured, `swagger-ph` will create one automatically.

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

```php
/**
 * @OA\Schema(
 *      schema="StringList",
 *      @OA\Property(property="value", type="array", @OA\Items(anyOf={@OA\Schema(type="string")}))
 * )
 * @OA\Schema(
 *      schema="String",
 *      @OA\Property(property="value", type="string")
 * )
 * @OA\Schema(
 *      schema="Object",
 *      @OA\Property(property="value", type="object")
 * )
 * @OA\Schema(
 *     schema="mixedList",
 *     @OA\Property(property="fields", type="array", @OA\Items(oneOf={
 *         @OA\Schema(ref="#/components/schemas/StringList"),
 *         @OA\Schema(ref="#/components/schemas/String"),
 *         @OA\Schema(ref="#/components/schemas/Object")
 *     }))
 * )
 */
```

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

```php
/**
 * @OA\SecurityScheme(
 *     type="apiKey",
 *     name="api_key",
 *     in="header",
 *     securityScheme="api_key"
 * )
 * 
 * @OA\SecurityScheme(
 *   type="oauth2",
 *   securityScheme="petstore_auth",
 *   @OA\Flow(
 *      authorizationUrl="http://petstore.swagger.io/oauth/dialog",
 *      flow="implicit",
 *      scopes={
 *         "read:pets": "read your pets",
 *         "write:pets": "modify pets in your account"
 *      }
 *   )
 * )
 */
```


To declare an endpoint as secure and define what security schemes are available to authenticate a client it needs to be
added to the operation, for example:
```php
/**
 * @OA\Get(
 *      path="/api/secure/",
 *      summary="Requires authentication"
 *    ),
 *    security={ {"api_key": {}} }
 * )
 */
```

::: tip Endpoints can support multiple security schemes and have custom options too:
```php
/**
 * @OA\Get(
 *      path="/api/secure/",
 *      summary="Requires authentication"
 *    ),
 *    security={
 *      { "api_key": {} },
 *      { "petstore_auth": {"write:pets", "read:pets"} }
 *    }
 * )
 */
```
:::

## File upload with headers
```php
/**
 * @OA\Post(
 *   path="/v1/media/upload",
 *   summary="Upload document",
 *   description="",
 *   tags={"Media"},
 *   @OA\RequestBody(
 *     required=true,
 *     @OA\MediaType(
 *       mediaType="application/octet-stream",
 *       @OA\Schema(
 *         required={"content"},
 *         @OA\Property(
 *           description="Binary content of file",
 *           property="content",
 *           type="string",
 *           format="binary"
 *         )
 *       )
 *     )
 *   ),
 *   @OA\Response(
 *     response=200, description="Success",
 *     @OA\Schema(type="string")
 *   ),
 *   @OA\Response(
 *     response=400, description="Bad Request"
 *   )
 * )
 */
```
## Set the XML root name

The `OA\Xml` annotation may be used to set the XML root element for a given `@OA\XmlContent` response body

```php
/**
 * @OA\Schema(
 *     schema="Error",
 *     @OA\Property(property="message"),
 *     @OA\Xml(name="details")
 * )
 */

/**
 * @OA\Post(
 *     path="/foobar",
 *     @OA\Response(
 *         response=400,
 *         description="Request error",
 *         @OA\XmlContent(ref="#/components/schemas/Error",
 *           @OA\Xml(name="error")
 *        )
 *     )
 * )
 */
 ```

## upload multipart/form-data
Form posts are `@OA\Post` requests with a `multipart/form-data` `@OA\RequestBody`. The relevant bit looks something like this
```php
/**
 * @OA\Post(
 *   path="/v1/user/update",
 *   summary="Form post",
 *   @OA\RequestBody(
 *     @OA\MediaType(
 *       mediaType="multipart/form-data",
 *       @OA\Schema(
 *         @OA\Property(property="name"),
 *         @OA\Property(
 *           description="file to upload",
 *           property="avatar",
 *           type="string",
 *           format="binary",
 *         ),
 *       )
 *     )
 *   ),
 *   @OA\Response(response=200, description="Success")
 * )
 */
```

## Default security scheme for all endpoints
Unless specified each endpoint needs to declare what security schemes it supports. However, there is a way to
to also configure security schemes globally for the whole API.

This is done on the `@OA\OpenApi` annotations:
```php
/**
 * @OA\OpenApi(
 *   security={{"bearerAuth": {}}}
 * )
 *
 * @OA\SecurityScheme(
 *   securityScheme="bearerAuth",
 *   type="http",
 *   scheme="bearer",
 * )
 */
```

## Nested objects
Complex, nested data structures are defined by nesting `@OA\Property` annotations inside others (with `type="object"`).
```php
/**
 *  @OA\Schema(
 *    schema="Profile",
 *    type="object",
*     
 *    @OA\Property(
 *      property="Status",
 *      type="string",
 *      example="0"
 *    ),
 *
 *    @OA\Property(
 *      property="Group",
 *      type="object",
 *
 *      @OA\Property(
 *        property="ID",
 *        description="ID de grupo",
 *        type="number",
 *        example=-1
 *      ),
 *
 *      @OA\Property(
 *        property="Name",
 *        description="Nombre de grupo",
 *        type="string",
 *        example="Superadmin"
 *      )
 *    )
 *  )
 */

```

## Documenting union type response data using `oneOf`
A response with either a single or a list of `QualificationHolder`'s.
```php
/**
 * @OA\Response(
 *     response=200,
 *     @OA\JsonContent(
 *         oneOf={
 *             @OA\Schema(ref="#/components/schemas/QualificationHolder"),
 *             @OA\Schema(
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/QualificationHolder")
 *             )
 *         }
 *     )
 * )
 */
```

## Reusing responses
Global responses are found under `/components/responses` and can be referenced/shared just like schema definitions (models) 

```php
/**
 * @OA\Response(
 *   response="product",
 *   description="All information about a product",
 *   @OA\JsonContent(ref="#/components/schemas/Product")
 * )
 */
class ProductResponse {}
 
 // ...

class ProductController
{
    /**
     * @OA\Get(
     *   tags={"Products"},
     *   path="/products/{product_id}",
     *   @OA\Response(
     *       response="default",
     *       ref="#/components/responses/product"
     *   )
     * )
     */
    public function getProduct($id)
    {
    }
}
```

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
The API does incllude basic support for callbacks. However, this needs to be set up mostly manually.

**Example**
```php
/**
 *     ...
 * 
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
 * 
 *     ...
 * 
 */
```

## (Mostly) virtual models
Typically a model is annotated by adding a `@OA\Schema` annotation to the class and then individual `@OA\Property` annotations
to the individually declared class properties.

It is possible, however, to nest `O@\Property` annotations inside a schema even without properties. In fact, all that is needed
is a code anchor - e.g. an empty class.

```php
use OpenApi\Attributes as OA;

#[OA\Schema(
    properties: [
        'name' => new OA\Property(property: 'name', type: 'string'),
        'email' => new OA\Property(property: 'email', type: 'string'),
    ]
)]
class User {}
```

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

First of all, when using custom schema names (`schema: 'user'`), this needs to be taken into account everywhere.
Secondly, having to write `ref: '#/components/schemas/user'` is tedious and error-prone.

Using attributes all this changes as we can take advantage of PHP itself by referring to a schema by its (fully qualified)
class name.

With the same `User` schema as before, the `Book::author` property could be written in a few different ways

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
    #[OAT\Property(type: User::class)]
    public author;
```

## Enums
As of PHP 8.1 there is native support for `enum`'s.

`swagger-php` supports enums in much the same way as class names can be used to reference schemas.

**Example**

```php
#[Schema()]
enum State
{
    case OPEN;
    case MERGED;
    case DECLINED;
}

#[Schema()]
class PullRequest
   #[OAT\Property()]
   public State $state
}
```

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

```php
/**
 * @OA\Get(
 *     path="/api/endpoint",
 *     description="The endpoint",
 *     operationId="endpoint",
 *     tags={"endpoints"},
 *     @OA\Parameter(
 *         name="things[]",
 *         in="query",
 *         description="A list of things.",
 *         required=false,
 *         @OA\Schema(
 *             type="array",
 *             @OA\Items(type="integer")
 *         )
 *     ),
 *     @OA\Response(response="200", description="All good")
 * )
 */
```

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
