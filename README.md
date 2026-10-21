[![Build Status](https://img.shields.io/github/actions/workflow/status/zircote/swagger-php/build.yml?branch=master)](https://github.com/zircote/swagger-php/actions?query=workflow:build)
[![Total Downloads](https://img.shields.io/packagist/dt/zircote/swagger-php.svg)](https://packagist.org/packages/zircote/swagger-php)
[![License](https://img.shields.io/badge/license-Apache2.0-blue.svg)](LICENSE)

# swagger-php

Generate interactive [OpenAPI](https://www.openapis.org) documentation for your RESTful API
using [PHP attributes](https://www.php.net/manual/en/language.attributes.overview.php).

See the [documentation website](https://zircote.github.io/swagger-php/guide/spec-attributes) for supported
attributes.

## Features

- Compatible with the OpenAPI **3.0**, **3.1** and **3.2** specification.
- Extracts information from code and existing phpdoc comments.
- Can be used programmatically or via command-line tool.
- Error reporting (with hints, context).
- **Spec attributes**: attributes that can also be built by hand and compiled without scanning, and a pipeline that reports when an attribute is wrong. See [Why spec attributes?](https://zircote.github.io/swagger-php/guide/why-spec).

## OpenAPI version support

`swagger-php` can generate specs for **OpenAPI 3.0.0**, **OpenAPI 3.1.0** and **OpenAPI 3.2.0**.
Classic mode defaults to `3.0.0`, spec and hybrid mode to `3.1.0`.

The command line option `--version` selects a different version; programmatically, use
`Builder::setVersion()`.

## Requirements

`swagger-php` requires at least **PHP 8.2**.

## Installation (with [Composer](https://getcomposer.org))

```shell
composer require zircote/swagger-php
```

For cli usage from anywhere, install swagger-php globally and add Composer's global binary directory
(`composer global config bin-dir --absolute`) to your PATH, so the `openapi` executable can be located by
your system.

```shell
composer global require zircote/swagger-php
```

### doctrine/annotations (deprecated)

As of version `4.8` the [doctrine annotations](https://www.doctrine-project.org/projects/annotations.html) library **is
optional** and **no longer installed by default**.

If your code uses doctrine annotations you will need to install that library manually:

```shell
composer require doctrine/annotations
```

## Usage

Use OpenAPI attributes to add metadata to your classes, methods and other structural PHP elements.

```php
use OpenApi\Spec as OA;

#[OA\Info(title: 'My First API', version: '0.1')]
class MyApi
{
    #[OA\Operation\Get(path: '/api/resource.json')]
    #[OA\Response(response: 200, description: 'An example resource')]
    public function getResource()
    {
        // ...
    }
}
```

Visit the [Documentation website](https://zircote.github.io/swagger-php/) for
the [Getting started guide](https://zircote.github.io/swagger-php/guide) or look at
the [examples directory](docs/examples) for more examples.

### Classic attributes and annotations

**Deprecated since 7.0, removed in 8.0.**

The attributes in `OpenApi\Attributes` and the docblock annotations still work. They run in hybrid mode,
which scans them and hands them to the same pipeline as the attributes above.

```php
use OpenApi\Attributes as OAT;

#[OAT\Info(title: 'My First API', version: '0.1')]
class MyApi
{
    #[OAT\Get(path: '/api/resource.json')]
    #[OAT\Response(response: '200', description: 'An example resource')]
    public function getResource()
    {
        // ...
    }
}
```

[Migrating to spec attributes](https://zircote.github.io/swagger-php/guide/migrating-to-spec) covers the move,
and [Processing Modes](https://zircote.github.io/swagger-php/guide/modes) compares the modes.

### Usage from PHP

Generate always-up-to-date documentation.

```php
<?php
require("vendor/autoload.php");
$result = (new \OpenApi\Builder())
    ->addSource(['/path/to/project'])
    ->build();
header('Content-Type: application/x-yaml');
echo $result->toYaml();
```

`Builder` is the entry point, and one instance builds one document.
See the [Builder reference](https://zircote.github.io/swagger-php/reference/builder) for its API.

Details on how to generate OpenApi specifications can be found
in the [generate reference](https://zircote.github.io/swagger-php/guide/generating-openapi-documents).

### Usage from the Command Line Interface

The `openapi` command line interface can be used to generate the documentation to a static yaml/json file.

```shell
./vendor/bin/openapi --help
```

## Automatic type resolution

Types are resolved with the `symfony/type-info` library. `swagger-php` supports all native type hints and also
generic type hints in phpdoc blocks. This simplifies the definition of schemas.

For example, the following two examples are equivalent:

```php
class MyClass
{
    #[OA\Schema\Items(oneOf: [
        new OA\Schema(ref: SchemaOne::class),
        new OA\Schema(ref: SchemaTwo::class),
    ])]
    public array $values;
}
```

```php
class MyClass
{
    /**
     * @var list<SchemaOne|SchemaTwo>
     */
    #[OA\Property]
    public array $values;
}
```

## [Contributing](CONTRIBUTING.md)

## More on OpenApi & Swagger

- https://swagger.io
- https://www.openapis.org
- [OpenApi Documentation](https://swagger.io/docs/)
- [OpenApi Specification](http://swagger.io/specification/)
- [Related projects](docs/related-projects.md)
