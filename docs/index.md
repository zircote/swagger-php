---
home: true
actionText: Get Started →
actionLink: /Getting-started
features:
  - title: OpenAPI specification
    details: Compatible with the OpenAPI Specification version 3.
      formerly known as Swagger.
  - title: "Incode your php file"
    details: "Using #[Attributes] or @Annotations lets you write the documentation inside the php source files which helps keeping the documentation in sync."
  - title: Useful error messages
    details: Enhanced errors messages with hints and context.
---

Install with composer:

```bash
composer require zircote/swagger-php
```

Create a php file:

```php
<?php
require("vendor/autoload.php");
$openapi = \OpenApi\Generator::scan(['/path/to/project']);
header('Content-Type: application/x-yaml');
echo $openapi->toYaml();
```

Add annotations to your php files.

```php

use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="My First API", version="0.1")
 */

/**
 * @OA\Get(
 *     path="/api/resource.json",
 *     @OA\Response(response="200", description="An example resource")
 * )
 */
```

Or, as of PHP 8.1 use attributes.

```php

use OpenApi\Attributes as OA;

#[OA\Info(title="My First API", version="0.1")]
class OpenApi {}

class Controller{
    #[OA\Get(path: '/api/resource.json')]
    #[OA\Response(response: '200', description: 'An example resource')]
    public function getResource()
    {
       // ...
    }
}
```

And view and interact with your API using [Swagger UI ](https://swagger.io/tools/swagger-ui/)

## Links

- [Getting started guide](Getting-started.md)
- [OpenApi Documentation](https://swagger.io/docs/)
- [OpenApi Specification](http://swagger.io/specification/)
- [Migration from 2.x to 3.x](Migrating-to-v3.md)
- [Migration from 3.x to 4.x](Migrating-to-v4.md)
- [Learn by example](https://github.com/zircote/swagger-php/tree/master/Examples) lots of example of how to generate
- [Related projects](Related-projects.md)
- [Swagger-php 2.x documentation](https://github.com/zircote/swagger-php/tree/2.x/docs) The docs for swagger-php v2
- [Swagger-php 1.x documentation](/1.x/) The docs for swagger-php v1
