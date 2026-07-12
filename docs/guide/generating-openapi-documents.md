# Generating OpenAPI documents

## Using the command line

`swagger-php` includes a command line tool `./vendor/bin/openapi`. This can be used to generate OpenAPI documents.

```shell
> ./vendor/bin/openapi app -o openapi.yaml
```

::: tip Output Format
By default, the output format is YAML. If a filename is given (via `--output` or `-o`)
the tool will use the file extension to determine the format.

The `--format` option can be used to force a specific format.
:::

::: tip Bootstrap
The bootstrap option `-b` is useful when trying to use `swagger-php` without proper autoloading.

For example, you might want to evaluate the library using a single file with just a few annotations.
In this case telling swagger-php to bootstrap (preload) the file prior to processing it will ensure
PHP's `reflection` code will be able to inspect your code.

```shell
> ./vendor/bin/openapi -b my_file.php my_file.php
```
:::


For a list of all available options use the `-h` option

```shell
> ./vendor/bin/openapi -h

Usage: openapi [--option value] [/path/to/project ...]

Options:
  --config (-c)               Generator config.
                              ex: -c operationId.hash=false
  --defaults (-D)             Show default config.
  --output (-o)               Path to store the generated documentation.
                              ex: --output openapi.yaml
  --exclude (-e)              Exclude path(s).
                              ex: --exclude vendor,library/Zend
  --pattern (-n)              Pattern of files to scan.
                              ex: --pattern "*.php" or --pattern "/\.(phps|php)$/"
  --bootstrap (-b)            Bootstrap php file(s) for defining constants, etc.
                              ex: --bootstrap config/constants.php
  --add-processor (-a)        Register an additional processor (allows multiple).
  --remove-processor (-r)     Remove an existing processor (allows multiple).
  --format (-f)               Force yaml or json.
  --mode (-m)                 Processing mode; "classic" uses the annotation/attribute pipeline.
  --debug (-d)                Show additional error information.
  --version                   The OpenAPI version; defaults to 3.0.0.
  --help (-h)                 Display this help message.
```

## Using PHP

Depending on your use case, PHP code can also be used to generate OpenAPI documents in a more dynamic way.

### Using the Builder

The `Builder` class is the recommended entry point for generating OpenAPI documents from PHP code.

```php
<?php
require('vendor/autoload.php');

$result = (new \OpenApi\Builder())
    ->addSource('/path/to/project')
    ->build();

header('Content-Type: application/x-yaml');
echo $result->toYaml();
```

The result object provides access to the generated spec in multiple formats, the list of scanned
files, and any validation warnings or errors collected during generation.

```php
$result->toYaml();      // YAML string
$result->toJson();      // JSON string
$result->toArray();     // PHP array
$result->files();       // list of scanned files
$result->warnings();    // validation warnings
$result->errors();      // validation errors
$result->isValid();     // true if spec was generated
```

For advanced Generator configuration (custom analysers, processors, aliases, etc.), use the
`withGenerator()` hook:

```php
$result = (new \OpenApi\Builder())
    ->addSource('/path/to/project')
    ->setVersion('3.1.0')
    ->withGenerator(function (\OpenApi\Generator $generator) {
        $generator->setConfig(['operationId.hash' => false]);
        $generator->withProcessorPipeline(function ($pipeline) {
            $pipeline->add(new MyCustomProcessor());
        });
    })
    ->build();
```

### Using the Generator directly

The `Generator` class can also be used directly:

```php
<?php
require('vendor/autoload.php');

$openapi = (new \OpenApi\Generator())->generate(['/path/to/project']);

header('Content-Type: application/x-yaml');
echo $openapi->toYaml();
```

::: tip Programming API
Details about the `swagger-php` API can be found in the [reference](../reference/index.md).
:::
