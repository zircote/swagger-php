# FAQ

## Warning: Required `@OA\Info()` not found

With adding support for [PHP attributes](https://www.php.net/manual/en/language.attributes.php) in version 4, some
architectural changes had to be made.

One of those changes is that placing annotations in your source files is now subject to the same limitations as attributes.
These limits are dictated by the PHP reflection API, specifically where it provides access to attributes and doc comments.

This means stand-alone annotations are no longer supported and ignored as `swagger-php` cannot _"see"_ them any more.

Supported locations:
* class
* interface
* trait
* method
* property
* class/interface const

Most commonly this manifests with a warning about the required `@OA\Info` not being found. While most annotations have specific
related code, the info annotation (and a few more) is kind of global.

The simplest solution to avoid this issue is to add a 'dummy' class to the docblock and add
all 'global' annotations (e.g. `Tag`, `Server`, `SecurityScheme`, etc.) **in a single docblock** to that class.

```php
/**
 * @OA\Tag(
 *     name="user",
 *     description="User related operations"
 * )
 * @OA\Info(
 *     version="1.0",
 *     title="Example API",
 *     description="Example info",
 *     @OA\Contact(name="Swagger API Team")
 * )
 * @OA\Server(
 *     url="https://example.localhost",
 *     description="API server"
 * )
 */
class OpenApiSpec
{
}
```

**As of version 4.8 the `doctrine/annotations` library is optional and might cause the same message.**

If this is the case, `doctrine annotations` must be installed separately:
```shell
composer require doctrine/annotations
```

## Annotations missing

Another side effect of using reflection is that `swagger-php` _"can't see"_ multiple consecutive docblocks any more as the PHP reflection API only provides access to the docblock closest to a given structural element.

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

In this case the simplest solution is to merge both docblocks. As an additional benefit the duplication of the summary can be avoided.

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
openapi: 3.0.0
paths:
  '/api/v0.0.2/notifications/{id}':
    delete:
      summary: 'XDelete notification by ID.'
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
With you application root being `myapp` you could try:

```shell
openapi -b myapp/vendor/autoload.php myapp/src
```

The `-b` allows to execute some extra PHP code to load whatever is needed to register your apps classloader with PHP.

::: warning
One common case for this type of error is when trying out annotations in a standalone single file.
Typically, this means it will not use namespaces or confirm to any other autoloading standards.

In this case the `-b` option can also be used to autoload the actual file.

Please note that you still need to provide the file (or its folder) as the target.

```shell
openapi -b src/test.php src/test.php
```
:::


### Namespace mismatch

Another reason for this error could be that your class actually has the wrong namespace (or no namespace at all!).

Depending on your framework this might still work in the context of your app, but the composer autoloader
alone might not be able to load your class (assuming you are using composer).

## No output from `openapi` command line tool

Depending on your PHP configuration, running the `openapi` command line tool might result in no output at all.

The reason for this is that `openapi` currently uses the [`error_log`](https://www.php.net/manual/en/function.error-log.php)
function for all output.

So if this is configured to write to a file, then it will seem like the command is broken.
