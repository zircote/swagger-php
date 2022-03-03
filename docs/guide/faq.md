# FAQ

## Warning: Required `@OA\Info()` not found

With adding support for [PHP attributes](https://www.php.net/manual/en/language.attributes.php) in version 4, some
architectural changes had to be made.

One of those changes is that placing annotations in your source files is now subject to the same limitations as attributes.
These limits are dictated by the PHP reflection API, specifically where it provides access to attributes and doc comments.

This means stand-alone annotations are no longer supported and ignored as `swagger-php` cannot 'see' them any more.

Most commonly this manifests with a warning about the required `@OA\Info` not being found. While most annotations have specifc
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

### Namespace mismatch

Another reason for this error could be that your class actually has the wrong namespace (or no namespace at all!).

Depending on your framework this might still work in the context of your app, but the composer autoloader 
alone might not be able to load your class (assuming you are using composer).
