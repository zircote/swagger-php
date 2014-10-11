
Changelog
----------

## 0.9.6

* Fix #169: Merge apis partials and then merge apis operation in one API (path) definition.
* Fix #163: Detect model inheritance for classes that are scanned in different paths.

## 0.9.5

* Implemented @SWG\Info & @SWG\Authorizations which can be used to augment the api-docs.json with metadata.
* Updated @link references to the github.com/wordnik/swagger-spec repository
* Reimplemented the petstore example using the updated json files from http://petstore.swagger.wordnik.com/api/api-docs
* Detect when an @SWG in a normal comment `//` or `/* */` instead of a DocBlock `/** */`
* Don't call hasPartialId() on non-swagger annotations (https://github.com/zircote/swagger-php/pull/160)

## 0.9.4

* Fixed regressions #145 & #146

## 0.9.3

* New Context/ProcessorInterface allows access to the parse context via the Annotation #142
* Added context to DocParser error messages and position is converted back into line and character information. #124
* Fixed model inheritance bug #140.
* getResourceList() now has a template option, which accepts a json file which can contain the `info` and `authentication` fields. #139
* The `--api-doc-template` option will use the imported `swaggerVersion`, `apiVersion` and `basePath` as defaults.

## 0.9.2

* Fixed issue with PHP 5.5  class name resolution (ClassName::class)

## 0.9.1

* Generate ".{format}" instead of ".json" suffix when using the commandline.
* Fixed bootstrap option
* Fixed missing use statements.

## 0.9.0

* New processing architecture allowing swagger-php to act on other annotations (https://github.com/zircote/swagger-php/pull/121)
* Improved model referencing: $ref allowed with complex modelnames (https://github.com/zircote/swagger-php/pull/122 and https://github.com/zircote/swagger-php/pull/123)
* Updated dependencies creating a 50% smaller swagger.phar

## 0.8.3

excludePath fixes and improvements

## 0.8.2

Extract descriptions from the docblock for model, resources, properties and apis. furter reducing duplicate descriptions.
Fixes issues: #115 & #116

## 0.8.1

Detect @SWG\Parameter type and @SWG\Items based on `@var Type[]` phpdoc comments.

## 0.8.0

* Compatible with swagger-ui 2.0
* Upgraded annotations to the [Swagger 1.2 Specification](https://github.com/wordnik/swagger-spec/blob/master/versions/1.2.md)
  * Use swagger-php 0.7.x to generate 1.1 or older swagger specs.
* Simplified API.
  * Removed many methods from the Swagger class.
  * Swagger->getResource() and getResourceList() use an options array for defaults, etc.
  * Removed caching. (Use your framework cache the json output or generate the json files in a build step)
* Added support for scanning multiple folders. `bin/swagger folder1 folder2`
* Assumes SWG namespace by default. `use Swagger\Annotations as SWG;` is now optional.
* Added Examples (Which are used as Fixtures in the UnitTests)
* Rewritten UnitTests
* Pretty printed json output by default.

## 0.7.0

* Simplified CLI interface [#91](https://github.com/zircote/swagger-php/pull/91)
  * Cleaner output.
  * Cleaner consistent naming of the options.
  * Detects invalid options.
  * Example usage: `swagger.phar project/` scans all *.php files in the "project" folder and outputs the docs in the current directory.

* Support for partials [#101](https://github.com/zircote/swagger-php/pull/101)
  * Enables splitting an annotation.
  * Reuse an Annotation (DRY).
  * For examples see the tests/SwaggerTests/Fixtures3 folder.

* Misc fixes


## 0.6.0

* New architecture
  * Split Swagger class into Swagger & Parser.
    Parser focuses on extracting swagger comments and converting them into a valid Resource or Model objects.
    Swagger provides the public api and crawls the project folder.

  * Add Logger, instead of crashing with an Exception, Swagger now reports the error and skips entries that are invalid or incomplete.

  * Split the Annotation->toArray into:
    * __contruct() import the data extracted by doctrine. (Parser might augement this data)
    * validate() tries to create a optimal valid data-structure and returns false if the annotation can't be used.
    * jsonSerialize() serializing the Annotation to the format expected by swagger-ui.

  * The Swagger->repository now consists of a more predictable object-tree instead of errror prone array struct.


* User features
  * Running bin/swagger without arguments now shows the help.

  * All encapsulation annotations are now optional, and might be deprecated in the future.
	@Api(@Operations(@Operation(), @Operation())) is the same as @Api(@Operation(), @Operation())

  * Show human readable hints/notices instead of "undefined index xxxx" notices.

  * Augment Swagger comments with available data from class/property/method or remaining data in the doc-comment.
    * Example @Model() used the classname as id

  * An @Api can be nested inside a @Resource and @Property can be nested inside a @Model.

## Olders versions
Checkout the [commit log](https://github.com/zircote/swagger-php/commits) for the changes from 0.1 to 0.4
