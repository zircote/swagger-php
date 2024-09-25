[![Build Status](https://img.shields.io/github/actions/workflow/status/zircote/swagger-php/build.yml?branch=master)](https://github.com/zircote/swagger-php/actions?query=workflow:build)
[![Total Downloads](https://img.shields.io/packagist/dt/zircote/swagger-php.svg)](https://packagist.org/packages/zircote/swagger-php)
[![License](https://img.shields.io/badge/license-Apache2.0-blue.svg)](LICENSE)

# swagger-php

Generate interactive [OpenAPI](https://www.openapis.org) documentation for your RESTful API using [PHP attributes](https://www.php.net/manual/en/language.attributes.overview.php) (preferred) or 
[doctrine annotations](https://www.doctrine-project.org/projects/annotations.html) (requires additional `doctrine/annotations` library).

See the [documentation website](https://zircote.github.io/swagger-php/guide/attributes.html) for supported attributes and annotations.

Annotations are deprecated and may be removed in a future release of swagger-php.

## Features

- Compatible with the OpenAPI **3.0** and **3.1** specification.
- Extracts information from code & existing phpdoc annotations.
- Command-line interface available.
- [Documentation site](https://zircote.github.io/swagger-php/) with a getting started guide.
- Exceptional error reporting (with hints, context)
- As of PHP 8.1 all annotations are also available as PHP attributes

## OpenAPI version support

`swagger-php` allows to generate specs either for **OpenAPI 3.0.0** or **OpenAPI 3.1.0**.
By default the spec will be in version `3.0.0`. The command line option `--version` may be used to change this
to `3.1.0`.

Programmatically, the method `Generator::setVersion()` can be used to change the version.

## Requirements

`swagger-php` requires at least PHP 7.2 for annotations and PHP 8.1 for using attributes.

## Installation (with [Composer](https://getcomposer.org))

```shell
composer require zircote/swagger-php
```

For cli usage from anywhere install swagger-php globally and make sure to place the `~/.composer/vendor/bin` directory in your PATH so the `openapi` executable can be located by your system.

```shell
composer global require zircote/swagger-php
```

### doctrine/annotations
As of version `4.8` the [doctrine annotations](https://www.doctrine-project.org/projects/annotations.html) library **is optional** and **no longer installed by default**.

To use PHPDoc annotations this needs to be installed on top of `swagger-php`:
```shell
composer require doctrine/annotations
```

If your code uses PHPDoc annotations you will need to install this as well:

```shell
composer require doctrine/annotations
```


## Usage

Add annotations to your php files.

```php
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

Visit the [Documentation website](https://zircote.github.io/swagger-php/) for the [Getting started guide](https://zircote.github.io/swagger-php/guide) or look at the [Examples directory](Examples/) for more examples.

### Usage from php

Generate always-up-to-date documentation.

```php
<?php
require("vendor/autoload.php");
$openapi = \OpenApi\Generator::scan(['/path/to/project']);
header('Content-Type: application/x-yaml');
echo $openapi->toYaml();
```
Documentation of how to use the `Generator` class can be found in the [Generator reference](https://zircote.github.io/swagger-php/reference/generator).

### Usage from the Command Line Interface

The `openapi` command line interface can be used to generate the documentation to a static yaml/json file.

```shell
./vendor/bin/openapi --help
```
Starting with version 4 the default analyser used on the command line is the new `ReflectionAnalyser`.

Using the `--legacy` flag (`-l`) the legacy `TokenAnalyser` can still be used.

### Usage from the Deserializer

Generate the OpenApi annotation object from a json string, which makes it easier to manipulate objects programmatically.

```php
<?php

use OpenApi\Serializer;

$serializer = new Serializer();
$openapi = $serializer->deserialize($jsonString, 'OpenApi\Annotations\OpenApi');
echo $openapi->toJson();
```

## [Contributing](CONTRIBUTING.md)

## More on OpenApi & Swagger

- https://swagger.io
- https://www.openapis.org
- [OpenApi Documentation](https://swagger.io/docs/)
- [OpenApi Specification](http://swagger.io/specification/)
- [Related projects](docs/related-projects.md)
