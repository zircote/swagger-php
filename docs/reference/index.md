# Reference

In total there are a number of different aspects to using `swagger-php`. Depending on how custom your requirements are this might be limited to just annotating your code and using the command line tool.

However, `swagger-php` offers a lot more.

* [Attributes](attributes.md)

  The new way of adding meta-data to your codebase. Requires PHP 8.1 

* [Docblock annotations](annotations.md)

  The 'traditional' way of documenting your API.  

* The [`Generator`](generator.md)

  The `\OpenAPI\Generator` class is the main entry point to programmatically generate OpenAPI documents from your code.

* [Processors](processors.md)

  `swagger-php` comes with a list of pre-defined processors that convert the raw data to a
  complete OpenAPI document.
  Custom processors can be added or existing removed to tweak swagger-php` to your requirements.
