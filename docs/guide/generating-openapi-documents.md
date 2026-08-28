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

To use spec mode from the CLI, pass `--mode spec`:

```shell
> ./vendor/bin/openapi app --mode spec -o openapi.yaml
```

::: tip Bootstrap
The bootstrap option `-b` is useful when trying to use `swagger-php` without proper autoloading.

For example, you might want to evaluate the library using a single file with just a few annotations.
In this case telling swagger-php to bootstrap (preload) the file prior to processing it will ensure
PHP's `reflection` code will be able to inspect your code.

```shell
> ./vendor/bin/openapi -b my_file.php my_file.php
```
:::


For a list of all available options use the `-h` option:

```shell
> ./vendor/bin/openapi -h

Description:
  Generate OpenAPI documentation

Usage:
  openapi [options] [--] <paths>...

Arguments:
  paths                                    Source path(s) to scan

Options:
  -c, --config=CONFIG                      Generator/Augmenter config; keys differ per mode, see -D (e.g. -c operationId.hash=false) (multiple values allowed)
  -D, --defaults                           Show default config
  -o, --output=OUTPUT                      Path to store the generated documentation (e.g. -o openapi.yaml)
  -f, --format=FORMAT                      Force yaml or json [default: "auto"]
  -e, --exclude=EXCLUDE                    Exclude path(s) (e.g. -e vendor -e library/Zend) (multiple values allowed)
  -n, --pattern=PATTERN                    Pattern of files to scan (e.g. -n "/\.(phps|php)$/") [default: "*.php"]
  -b, --bootstrap=BOOTSTRAP                Bootstrap php file(s) for defining constants, etc. (e.g. -b config/constants.php) (multiple values allowed)
  -a, --add-processor=ADD-PROCESSOR        Register an additional processor (multiple values allowed)
  -r, --remove-processor=REMOVE-PROCESSOR  Remove an existing processor (multiple values allowed)
      --version=VERSION                    The OpenAPI version
  -m, --mode=MODE                          Set mode classic, hybrid or spec [default: "classic"]
  -d, --debug                              Show additional error information
  -h, --help                               Display help for the given command. When no command is given display help for the list command
      --silent                             Do not output any message
  -q, --quiet                              Only errors are displayed. All other output is suppressed
      --ansi|--no-ansi                     Force (or disable --no-ansi) ANSI output
  -v|vv|vvv, --verbose                     Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
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
$result->toYaml();        // YAML string
$result->toJson();        // JSON string
$result->toArray();       // PHP array
$result->files();         // list of scanned files
$result->warnings();      // validation warnings
$result->errors();        // validation errors
$result->isValid();       // true if spec was generated and no errors were reported
$result->specification(); // the final `Specification` instance
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

### Using spec mode

To use the new spec attributes pipeline (beta), set the mode to `spec`:

```php
<?php
require('vendor/autoload.php');

$result = (new \OpenApi\Builder())
    ->setMode(\OpenApi\Builder\Mode::SPEC)
    ->addSource('/path/to/project')
    ->build();

header('Content-Type: application/x-yaml');
echo $result->toYaml();
```

Spec mode uses attributes from the `OpenApi\Spec` namespace. See [Using Spec Attributes](/guide/spec-attributes) for details on the attribute API and [Processing Modes](/guide/modes) for a comparison of all modes.

In spec and hybrid mode, you can also pass `\ReflectionClass` instances directly instead of file paths:

```php
<?php
require('vendor/autoload.php');

$result = (new \OpenApi\Builder())
    ->setMode(\OpenApi\Builder\Mode::SPEC)
    ->addSource([
        new \ReflectionClass(App\Controllers\PetController::class),
        new \ReflectionClass(App\Models\Pet::class),
    ])
    ->build();

echo $result->toYaml();
```

### Using the Generator directly

The `Generator` class can also be used directly (classic mode only):

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
