# swagger-php

[![Build Status](https://api.travis-ci.org/zircote/swagger-php.png?branch=master)](http://travis-ci.org/zircote/swagger-php) `master`

Generate interactive [Swagger](http://swagger.io) documentation for your RESTful API using [doctrine annotations](http://doctrine-common.readthedocs.org/en/latest/reference/annotations.html).

## Features

 - Compatible with the Swagger 2.0 specification.
 - Exceptional error reporting (with hints, context)
 - Extracts information from code & existing phpdoc annotations.
 - Command-line interface available.

## Installation (with [Composer](http://composer.org))

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
 *     @SWG\Response(name="200", description="An example resource")
 * )
 */
```
See the Examples directory for more.

### Usage from php

Generate always-up-to-date the swagger documentation dynamicly.

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

## More on Swagger

  * http://swagger.io/
  * https://github.com/swagger-api/swagger-spec/
  * http://bfanger.github.io/swagger-explained/

## Contributing

Please feel free to submit [Github Issues](https://github.com/zircote/swagger-php/issues) or pull requests.
The documentation website resides within the `gh-pages` branch.

[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/zircote/swagger-php/trend.png)](https://bitdeli.com/free "Bitdeli Badge")
