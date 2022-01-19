# Attributes

::: tip Namespace
Using a namespace alias simplifies typing and improves readability.

All attributes are in the `OpenApi\Attributes` namespace.
:::

## Nesting

Similar to annotations attributes can be top level or nested. However, attributes **may be put at the same level** if
there is no ambiguity. `swagger-php` will then merge attributes according to the defined rules about parent/child
relationships.

**Example**

Nested:
```php
    #[OA\Get(
        path: '/api/users',
        responses: [
            new OA\Response(response: 200, description: 'AOK'),
            new OA\Response(response: 401, description: 'Not allowed'),
        ]
    )]
    public function users() { /* ... */ }
```

Not nested:
```php
    #[OA\Get(path: '/api/users')]
    #[OA\Response(response: 200, description: 'AOK')]
    #[OA\Response(response: 401, description: 'Not allowed')]
    public function users() { /* ... */ }
```

Depending on how much nesting there is this can make things a bit simpler and easier to read.

::: warning Top level only
Automatic merging of attributes works only at the top level - in the example that would be the method `users()`.
:::
