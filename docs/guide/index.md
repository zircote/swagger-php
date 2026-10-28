# What is Swagger-PHP?

`swagger-php` is a library that extracts API metadata from your PHP source code files.

The idea is to add `swagger-php` [attributes](using-attributes.md)
next to the relevant PHP code in your application. These will contain the details about your API and
`swagger-php` will convert those into machine-readable [OpenAPI documentation](https://spec.openapis.org/oas/v3.1.0.html).

Keeping your API documentation next to the corresponding source code (same file!) makes it easy to keep up-to-date,
as all details can be modified in one place.

::: tip Annotating
Annotating your code means adding metadata to the source code for `swagger-php`. This is done with
[attributes](using-attributes.md), classic attributes or [docblock annotations](using-annotations.md).
:::

::: warning Requirements
Using `swagger-php` requires a minimum of **PHP&nbsp;8.2**.
:::
