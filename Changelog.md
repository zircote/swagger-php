# Changelog

## 2.0.12

 - Added field allowEmptyValue #451
 - Improved compatibility with windows line-breaks #433
 - Improved docs & validation #429, #450

## 2.0.11

 - Implemented Swagger->ref(). $swagger->ref('#/info/contact') === $swagger->info->contact
 - Added validation of internal refs.
 - Misc typos and improved unittests #357, #399, #402, #407, #423, #424
 - Misc improvements to the documentation.

## 2.0.10

 - Fix errors when parsing php7 anonymous class #380, #390
 - Added support for linked files and directories #393
 - Added support for $ref in deserialize() #369
 - Misc documentation tweaks.

## 2.0.9

 - Added support for PHP 7.1 and dropped support for PHP 5.5 and below
 - Added `composer test` script
 - Added support for custom DocParser->setImports() #360

## 2.0.8

 - Dynamic Definitions / partial support #301
 - By default only scan *.php files #350
 - Removed silence operator @, improves compatiblity custom errorhandlers #331
 - Additional datetime classes & interfaces #338
 - Fixed case of UNDEFINED constants namespaces, improves hhvm compatibility #319
 - Misc improvements to the docs

## 2.0.7

 - Beter validation of type="file" #305
 - Augment Operations summary and description based on the comment. #293
 - Bugfixes #300
 - Add support for xml (and externalDocs and properties) inside a @SWG\Items. #279
 - Nested properties are no longer injected into the Definition #297
 - Fixed nesting issue with verbose property notation #297

## 2.0.6

 - Added Deserializer for converting json string into a Swagger Annotations object. #290
 - Various readme improvements #284 #286
 - Various PSR-2 coding standard fixes #291
 - Travis now enforces passing PHP CodeSniffer PSR-2 tests #291
 - Use badges from shields.io #288

## 2.0.5

 - Removed JSON-Schema properties that are not supported in swagger. #273
 - Added ordering filenames to guarantee a consistent output #263

## 2.0.4

 - Fixed minor regression #254
 - Removed format restrictions from the remaining annotations #253

## 2.0.3

 - Removed format restrictions #253
 - Fixed a bug with inherited properties #250

## 2.0.2

 - Added support for @SWG\Head & @SWG\Options for documenting the HEAD and OPTIONS responses
 - Removed restrictions in @SWG\Items, allow @SWG\Property and other @SWG\Schema options.

## 2.0.1

 - Minimal support for traits (no inheritance)

## 2.0.0

 - Generates swagger.json with Swagger Specification v2
 - New simplified architecture (crawl -> analysis -> processors -> validation -> output)
