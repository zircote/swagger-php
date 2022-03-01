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
