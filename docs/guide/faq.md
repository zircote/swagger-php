# FAQ

## Error: `info is required`

Classic mode reports this as *Required `@OA\Info()` not found*.

`swagger-php` reads attributes and annotations through the PHP reflection API, so it only finds them where reflection
gives access to attributes and doc comments. A stand-alone docblock annotation is ignored.

Supported locations:
* class
* interface
* trait
* method
* property
* class/interface const

Most attributes belong to a specific piece of code. `OA\Info` (and a few more) is global, so there is no obvious place for
it, and it ends up nowhere.

The simplest solution is a 'dummy' class that holds all global attributes (e.g. `OA\Info`, `OA\Tag`, `OA\Server`).
With annotations they go **in a single docblock** on that class.

<codeblock id="dummy-class">
  <template v-slot:at>

<<< @/snippets/guide/faq/dummy_class_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/faq/dummy_class_an.php

  </template>
  <template v-slot:spec>

<<< @/snippets/guide/faq/dummy_class_spec.php

  </template>
</codeblock>

**Docblock annotations need the `doctrine/annotations` library**, which is not installed with `swagger-php`. Without it,
annotations are not read, and the same message appears:
```shell
composer require doctrine/annotations
```

## Annotations missing <Badge type="warning" text="deprecated" />

Another side effect of using reflection is that `swagger-php` _"can't see"_ multiple consecutive docblocks anymore as the PHP reflection API only provides access to the docblock closest to a given structural element.

```php
class Controller
{
    /**
     * @OA\Delete(
     *      path="/api/v0.0.2/notifications/{id}",
     *      operationId="deleteNotificationById",
     *      summary="Delete notification by ID",
     *      @OA\Parameter(name="id", in="path", @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="OK"),
     *      @OA\Response(response=400, description="Bad Request")
     * )
     */
    /**
     * Delete notification by ID.
     *
     * @param Request $request
     * @param AppNotification $notification
     *
     * @return Response
     * @throws Exception
     */
    public function destroy(Request $request, AppNotification $notification) {
        //
    }
}
```

In this case the simplest solution is to merge both docblocks. As an additional benefit, the duplication of the summary can be avoided.

In this improved version `swagger-php` will automatically use the docblock summary just as explicitly done above.

```php
class Controller
{
    /**
     * Delete notification by ID.
     *
     * @OA\Delete(
     *      path="/api/v0.0.2/notifications/{id}",
     *      operationId="deleteNotificationById",
     *      @OA\Parameter(name="id", in="path", @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="OK"),
     *      @OA\Response(response=400, description="Bad Request")
     * )
     *
     * @param Request $request
     * @param AppNotification $notification
     *
     * @return Response
     * @throws Exception
     */
    public function destroy(Request $request, AppNotification $notification) {
        //
    }
}
```

**Resulting spec:**
```yaml
openapi: 3.1.0
paths:
  '/api/v0.0.2/notifications/{id}':
    delete:
      summary: 'Delete notification by ID.'
      operationId: deleteNotificationById
      parameters:
        -
          name: id
          in: path
          schema:
            type: integer
      responses:
        '200':
          description: OK
        '400':
          description: 'Bad Request'

```

## Skipping unknown `\SomeClass`

This message means that `swagger-php` has tried to use reflection to inspect `\SomeClass` and that PHP could not find/load
that class. Effectively, this means that `class_exists("\SomeClass")` returns `false`.

### Using the `-b` `--bootstrap` option

There are a number of reasons why this could happen. If you are using the `openapi` command line tool from a global
installation typically the application classloader (composer) is not active.
With your application root being `myapp` you could try:

```shell
openapi -b myapp/vendor/autoload.php myapp/src
```

The `-b` allows to execute some extra PHP code to load whatever is needed to register your apps classloader with PHP.

::: warning
One common case for this type of error is when trying out annotations in a standalone single file.
Typically, this means it will not use namespaces or conform to any other autoloading standards.

In this case the `-b` option can also be used to autoload the actual file.

Please note that you still need to provide the file (or its folder) as the target.

```shell
openapi -b src/test.php src/test.php
```
:::


### Namespace mismatch

Another reason for this error could be that your class actually has the wrong namespace (or no namespace at all!).

Depending on your framework, this might still work in the context of your app, but the composer autoloader
alone might not be able to load your class (assuming you are using composer).

## Where the `openapi` output goes

Without `-o`, the `openapi` command line tool writes the document to stdout and all messages to stderr. Redirecting stdout
keeps the messages out of the file:

```shell
openapi src > openapi.yaml
```

A run that reports an error still writes the document, and exits with `1`. Warnings do not change the exit code.
