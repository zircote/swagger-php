[![Build Status](https://img.shields.io/github/actions/workflow/status/zircote/swagger-php/build.yml?branch=master)](https://github.com/zircote/swagger-php/actions?query=workflow:build)
[![Total Downloads](https://img.shields.io/packagist/dt/zircote/swagger-php.svg)](https://packagist.org/packages/zircote/swagger-php)
[![License](https://img.shields.io/badge/license-Apache2.0-blue.svg)](LICENSE)

# swagger-php

Generate interactive [OpenAPI](https://www.openapis.org) documentation for your RESTful API
using [PHP attributes](https://www.php.net/manual/en/language.attributes.overview.php) (preferred) or
[doctrine annotations](https://www.doctrine-project.org/projects/annotations.html) (requires additional
`doctrine/annotations` library).

See the [documentation website](https://zircote.github.io/swagger-php/guide/using-attributes.html) for supported
attributes and annotations.

**Annotations are deprecated and may be removed in a future release of swagger-php.**

## Features

- Compatible with the OpenAPI **3.0**, **3.1** and **3.2** specification.
- Extracts information from code and existing phpdoc comments.
- Can be used programmatically or via command-line tool.
- [Documentation site](https://zircote.github.io/swagger-php/) with a getting started guide.
- Error reporting (with hints, context).
- All metadata is configured via PHP attributes.

## OpenAPI version support

`swagger-php` allows to generate specs either for **OpenAPI 3.0.0**, **OpenAPI 3.1.0** and **OpenAPI 3.2.0**.
By default, the spec will be in version `3.0.0`. The command line option `--version` may be used to change to
any other supported version.

Programmatically, the method `Generator::setVersion()` can be used to change the version.

## Requirements

`swagger-php` requires at least **PHP 8.2**.

## Installation (with [Composer](https://getcomposer.org))

```shell
composer require zircote/swagger-php
```

For cli usage from anywhere, install swagger-php globally and make sure to place the `~/.composer/vendor/bin` directory
in your PATH so the `openapi` executable can be located by your system.

```shell
composer global require zircote/swagger-php
```

### doctrine/annotations

As of version `4.8` the [doctrine annotations](https://www.doctrine-project.org/projects/annotations.html) library **is
optional** and **no longer installed by default**.

If your code uses doctrine annotations you will need to install that library manually:

```shell
composer require doctrine/annotations
```

## Usage

Use OpenAPI attributes to add metadata to your classes, methods and other structural PHP elements.

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

Visit the [Documentation website](https://zircote.github.io/swagger-php/) for
the [Getting started guide](https://zircote.github.io/swagger-php/guide) or look at
the [examples directory](docs/examples) for more examples.

### Usage from PHP

Generate always-up-to-date documentation.

```php
<?php
require("vendor/autoload.php");
$openapi = (new \OpenApi\Generator->generate(['/path/to/project']);
header('Content-Type: application/x-yaml');
echo $openapi->toYaml();
```

Documentation of how to use the `Generator` class can be found in
the [Generator reference](https://zircote.github.io/swagger-php/reference/generator).

### Usage from the Command Line Interface

The `openapi` command line interface can be used to generate the documentation to a static yaml/json file.

```shell
./vendor/bin/openapi --help
```

## Automatic type resolution

As of version 6, resolving of types is done using the `TypeInfoTypeResolver` class. It uses the `symfony/type-info`
library under the hood which allows handling of complext types.

With this change, `swagger-php` supports all available native type-hints and also complext generic type-hints via phpdoc
blocks.
This simplifies the definition of schemas.

For example, the following two examples are now equivalent:

```php
class MyClass
{
    #[OAT\Property(items: new OAT\Items(oneOf: [
        new OAT\Schema(type: SchemaOne::class),
        new OAT\Schema(type: SchemaTwo::class),
    ]))]
    public array $values;
}
```

```php
class MyClass
{
    /**
     * @var list<SchemaOne|SchemaTwo>
     */
    public array $values;
}
```

If this is not desired, the `LegacyTypeResolver` can be used to preserve the old behaviour of version 5.
The `LegacyTypeResolver` is deprecated and will be removed in a future release.

## [Contributing](CONTRIBUTING.md)

## More on OpenApi & Swagger

- https://swagger.io
- https://www.openapis.org
- [OpenApi Documentation](https://swagger.io/docs/)
- [OpenApi Specification](http://swagger.io/specification/)
- [Related projects](docs/related-projects.md)
