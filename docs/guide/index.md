# What is Swagger-PHP?

`swagger-php` is a library that extracts API metadata from your PHP source code files.

The idea is to add `swagger-php` [annotations](annotations.md) or [attributes](attributes.md)
next to the relevant PHP code in your application. These will contain the details about your API and
`swagger-php` will convert those into machine-readable [OpenAPI documentation](https://spec.openapis.org/oas/v3.1.0.html).

By adding your API documentation next to the corresponding source code (same file!) makes it easy to keep it up-to-date
as all details can be modified in one place.

::: tip Annotating vs. Annotations
When talking about annotating your code we mean the act of adding meta-data to your codebase. This can be done by
either adding [`Annotations`](annotations.md) or [`Attributes`](attributes.md).
:::

::: warning Requirements
Using `swagger-php` requires a minimum of **PHP&nbsp;7.4** for using annotations and
at least **PHP&nbsp;8.1** to use attributes.
:::
