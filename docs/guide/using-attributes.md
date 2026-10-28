# Attributes

PHP attributes describe the API next to the code they document. The attributes are in the
`OpenApi\Spec` namespace. [Using Spec Attributes](/guide/spec-attributes) covers them in detail.

::: tip Namespace
Using a namespace alias simplifies typing and improves readability.

```php
use OpenApi\Spec as OA;

#[OA\Info(/*...*/)]
```
:::

## Nesting

Attributes can be nested, or placed side by side on the same element. `swagger-php` merges
side-by-side attributes according to their parent/child relationships.

**Example**

Nested:
```php
    #[OA\Operation\Get(
        path: '/api/users',
        responses: [
            new OA\Response(response: 200, description: 'OK'),
            new OA\Response(response: 401, description: 'Not allowed'),
        ]
    )]
    public function users() { /* ... */ }
```

Not nested:
```php
    #[OA\Operation\Get(path: '/api/users')]
    #[OA\Response(response: 200, description: 'OK')]
    #[OA\Response(response: 401, description: 'Not allowed')]
    public function users() { /* ... */ }
```

Depending on how much nesting there is this can make things a bit simpler and easier to read.
The merge works only while it is unambiguous. [Responses and parameters](/guide/spec-attributes#responses-and-parameters)
has the rules.
