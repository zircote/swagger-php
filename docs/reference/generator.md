# Using the `Generator`

## Introduction
The `Generator` class provides an object-oriented way to use `swagger-php` and all its aspects in a single place.

## The `\OpenApi\scan()` function

For a long time the `\OpenApi\scan()` function was the main entry point into using swagger-php from PHP code.

```php
/**
  * Scan the filesystem for OpenAPI annotations and build openapi-documentation.
  *
  * @param array|Finder|string $directory The directory(s) or filename(s)
  * @param array               $options
  *                                       exclude: string|array $exclude The directory(s) or filename(s) to exclude (as absolute or relative paths)
  *                                       pattern: string       $pattern File pattern(s) to scan (default: *.php)
  *                                       analyser: defaults to StaticAnalyser
  *                                       analysis: defaults to a new Analysis
  *                                       processors: defaults to the registered processors in Analysis
  *
  * @return OpenApi
  */
  function scan($directory, $options = []) { /* ... */ }
```

Using it looked typically something like this:
```php
require("vendor/autoload.php");

$openapi = \OpenApi\scan(__DIR__, ['exclude' => ['tests'], 'pattern' => '*.php']);
```

The two configuration options for the underlying Doctrine doc-block parser `aliases` and `namespaces`
are not part of this function and need to be set separately. 

Being static this means setting them back is the callers responsibility and there is also the fact that 
some Doctrine configuration currently can not be reverted easily.

Therefore, having a single side effect free way of using swagger-php seemed like a good idea...

## The `\OpenApi\Generator` class

The `Generator` class can  be used in object-oriented (and fluent) style which allows for easy customization
if needed.

In that case to actually process the given input files the **non-static** method `generate()` is to be used.

Full example of using the `Generator` class to generate OpenApi specs.

```php
require("vendor/autoload.php");

$validate = true;
$logger = new \Psr\Log\NullLogger();
$processors = [/* my processors */];
$finder = \Symfony\Component\Finder\Finder::create()->files()->name('*.php')->in(__DIR__);

$openapi = (new \OpenApi\Generator($logger))
            ->setProcessors($processors)
            ->setAliases(['MY' => 'My\Annotations'])
            ->setNamespaces(['My\\Annotations\\'])
            ->setAnalyser(new \OpenApi\Analysers\TokenAnalyser())
            ->setVersion(\OpenApi\Annotations\OpenApi::VERSION_3_0_0)
            ->generate(['/path1/to/project', $finder], new \OpenApi\Analysis(), $validate);
```

`Aliases` and `namespaces` are additional options that allow to customize the parsing of docblocks.

Defaults:
* **aliases**: `['oa' => 'OpenApi\\Annotations']`

  Aliases help the underlying `doctrine annotations library` to parse annotations. Effectively they avoid having
  to write `use OpenApi\Annotations as OA;` in your code and make `@OA\property(..)` annotations still work.

* **namespaces**: `['OpenApi\\Annotations\\']`

  Namespaces control which annotation namespaces can be autoloaded automatically. Under the hood this
  is handled by registering a custom loader with the `doctrine annotation library`.

Advantages:
* The `Generator` code will handle configuring things as before in a single place
* Static settings will be reverted to the default once finished
* The get/set methods allow for using type hints
* Static configuration is deprecated and can be removed at some point without code changes
* Build in support for PSR logger
* Support for [Symfony Finder](https://symfony.com/doc/current/components/finder.html), `\SplInfo` and file/directory names (`string) as source.

The minimum code required, using the `generate()` method, looks quite similar to the old `scan()` code:

```php
    /**
     * Generate OpenAPI spec by scanning the given source files.
     *
     * @param iterable      $sources  PHP source files to scan.
     *                                Supported sources:
     *                                * string - file / directory name
     *                                * \SplFileInfo
     *                                * \Symfony\Component\Finder\Finder
     * @param null|Analysis $analysis custom analysis instance
     * @param bool          $validate flag to enable/disable validation of the returned spec
     */
    public function generate(iterable $sources, ?Analysis $analysis = null, bool $validate = true): \OpenApi\OpenApi { /* ... */ }
```

```php
require("vendor/autoload.php");

$openapi = (new \OpenApi\Generator())->generate(['/path1/to/project']);
```

For those that want to type even less and keep using a plain array to configure `swagger-php` there is also a static version:

```php
<?php
require("vendor/autoload.php");

$openapi = \OpenApi\Generator::scan(['/path/to/project']);

header('Content-Type: application/x-yaml');
echo $openapi->toYaml();
```

**Note:** While using the same name as the old `scan()` function, the `Generator::scan` method is not
100% backwards compatible.

```php
    /**
     * Static  wrapper around `Generator::generate()`.
     *
     * @param iterable $sources PHP source files to scan.
     *                          Supported sources:
     *                          * string
     *                          * \SplFileInfo
     *                          * \Symfony\Component\Finder\Finder
     * @param array    $options
     *                          aliases:    null|array                    Defaults to `['oa' => 'OpenApi\\Annotations']`.
     *                          namespaces: null|array                    Defaults to `['OpenApi\\Annotations\\']`.
     *                          analyser:   null|AnalyserInterface        Defaults to a new `ReflectionAnalyser` supporting both docblocks and attributes.
     *                          analysis:   null|Analysis                 Defaults to a new `Analysis`.
     *                          processors: null|array                    Defaults to `Analysis::processors()`.
     *                          logger:     null|\Psr\Log\LoggerInterface If not set logging will use \OpenApi\Logger as before.
     *                          validate:   bool                          Defaults to `true`.
     */
    public static function scan(iterable $sources, array $options = []): OpenApi { /* ... */ }
```

Most notably the `exclude` and `pattern` keys are no longer supported. Instead, a Symfony `Finder` instance can be passed in
as source directly (same as with `Generator::generate()`).

If needed, the `\OpenApi\Util` class provides a builder method that allows to keep the status-quo

```php
$exclude = ['tests'];
$pattern = '*.php';

$openapi = \OpenApi\Generator::scan(\OpenApi\Util::finder(__DIR__, $exclude, $pattern));

// same as

$openapi = \OpenApi\scan(__DIR__, ['exclude' => $exclude, 'pattern' => $pattern]);
