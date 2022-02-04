# Generating OpenAPI documents

## `./bin/openapi`

`swagger-php` includes a command line tool `./bin/openapi`. This can be used to generate OpenAPI documents.

```shell
> ./vendor/bin/openapi app -o openapi.yaml
```

::: tip Output Format
By default the output format is YAML. If a filename is given (via `--output` or `-o`) 
the tool will use the file extension to determine the format.

The `--format` option can be used to force a specifc format.
:::

For a list of all available options use the `-h` option 

```shell
> ./bin/openapi -h

Usage: openapi [--option value] [/path/to/project ...]

Options:
  --legacy (-l)     Use legacy TokenAnalyser; default is the new ReflectionAnalyser
  --output (-o)     Path to store the generated documentation.
                    ex: --output openapi.yaml
  --exclude (-e)    Exclude path(s).
                    ex: --exclude vendor,library/Zend
  --pattern (-n)    Pattern of files to scan.
                    ex: --pattern "*.php" or --pattern "/\.(phps|php)$/"
  --bootstrap (-b)  Bootstrap a php file for defining constants, etc.
                    ex: --bootstrap config/constants.php
  --processor       Register an additional processor.
  --format          Force yaml or json.
  --debug           Show additional error information.
  --version         The OpenAPI version; defaults to 3.0.0.
  --help (-h)       Display this help message.
```

## Using PHP

Depending on your use case PHP code can also be used to generate OpenAPI documents in a more dynamic way.

In its simplest form this may look something like

```php
<?php
require("vendor/autoload.php");

$openapi = \OpenApi\Generator::scan(['/path/to/project']);

header('Content-Type: application/x-yaml');
echo $openapi->toYaml();
```

::: tip Programming API
Details about the `swagger-php` API can be found in the [reference](../reference/index.md).
:::
