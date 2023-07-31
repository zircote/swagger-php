[![Build Status](https://img.shields.io/travis/zircote/swagger-php/master.svg?style=flat-square)](https://travis-ci.org/zircote/swagger-php)
[![Total Downloads](https://img.shields.io/packagist/dt/zircote/swagger-php.svg?style=flat-square)](https://packagist.org/packages/zircote/swagger-php)
[![License](https://img.shields.io/badge/license-Apache-blue.svg?style=flat-square)](LICENSE-2.0.txt)

# swagger-php

Generate interactive [Swagger](http://swagger.io) documentation for your RESTful API using [doctrine annotations](http://doctrine-common.readthedocs.org/en/latest/reference/annotations.html).

## Features

 - Compatible with the Swagger 2.0 specification.
 - Exceptional error reporting (with hints, context)
 - Extracts information from code & existing phpdoc annotations.
 - Command-line interface available.

## Installation (with [Composer](https://getcomposer.org))

```sh
composer require zircote/swagger-php
```

For cli usage from anywhere install swagger-php globally and make sure to place the `~/.composer/vendor/bin` directory in your PATH so the `swagger` executable can be located by your system.

```sh
composer global require zircote/swagger-php
```

## Usage

Add annotations to your php files.
```php
/**
 * @SWG\Info(title="My First API", version="0.1")
 */

/**
 * @SWG\Get(
 *     path="/api/resource.json",
 *     @SWG\Response(response="200", description="An example resource")
 * )
 */
```
See the [Getting started guide](docs/Getting-started.md) and [Examples directory](Examples/) for more examples.


### Usage from php

Generate always-up-to-date documentation.

```php
<?php
require("vendor/autoload.php");
$swagger = \Swagger\scan('/path/to/project');
header('Content-Type: application/json');
echo $swagger;
```
### Usage from the Command Line Interface

Generate the swagger documentation to a static json file.

```sh
./vendor/bin/swagger --help
```

### Usage from the Deserializer

Generate the swagger annotation object from a json string, which makes it easier to manipulate swagger object programmatically.

```php
<?php

use Swagger\Serializer;

$serializer = new Serializer();
$swagger = $serializer->deserialize($jsonString, 'Swagger\Annotations\Swagger');
echo $swagger;
```

### Usage from [docker](https://docker.com)

Generate the swagger documentation to a static json file.

```
docker run -v "$PWD":/app -it tico/swagger-php --help
```

## More on Swagger

  * http://swagger.io/
  * https://github.com/swagger-api/swagger-spec/
  * http://bfanger.github.io/swagger-explained/
  * [Related projects](docs/Related-projects.md)
  * https://www.marcoraddatz.com/en/2015/07/21/integrate-swagger-into-laravel/

## Contributing

Feel free to submit [Github Issues](https://github.com/zircote/swagger-php/issues)
or pull requests.

The documentation website resides within the `gh-pages` branch.

Make sure pull requests pass [PHPUnit](https://phpunit.de/)
and [PHP_CodeSniffer](https://github.com/cakephp/cakephp-codesniffer) (PSR-2) tests.

Running tests can be done with this command in the root of the project:

```bash
./bin/phpunit
```

To run the phpcs tests on your local machine execute:

```bash
./bin/phpcs -p --extensions=php --standard=PSR2 --error-severity=1 --warning-severity=0 ./src ./tests
```

To run both unittests and linting execute:

```bash
composer test
```

[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/zircote/swagger-php/trend.png)](https://bitdeli.com/free "Bitdeli Badge")
