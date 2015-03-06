# swagger-php

[![2.* Development Build Status](https://api.travis-ci.org/zircote/swagger-php.png?branch=2.x)](http://travis-ci.org/zircote/swagger-php) `2.*@dev`

Generate interactive [Swagger](http://swagger.io) documentation for your RESTful API using [doctrine annotations](http://doctrine-common.readthedocs.org/en/latest/reference/annotations.html).

## Features

 - Compatible with the Swagger 2.0 specification
 - Exceptional error reporting (with hints, context)
 - Extracts information from code & existing phpdoc annotations.

## Installation (with [Composer](http://composer.org))

```sh
composer require zircote/swagger-php
```

## Usage

```php
<?php
require("vendor/autoload.php");
$swagger = \Swagger\scan('/path/to/project');
echo $swagger;
```

## More on Swagger

  * http://swagger.io/
  * https://github.com/swagger-api/swagger-spec/

## Contributing

Please feel free to submit [Github Issues](https://github.com/zircote/swagger-php/issues) or pull requests.
The documentation website resides within the `gh-pages` branch.

[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/zircote/swagger-php/trend.png)](https://bitdeli.com/free "Bitdeli Badge")
