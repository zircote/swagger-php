# Plugins

Plugins that ship with other packages. Each one adds its translators, resolvers or augmenters
to a builder, so using it means installing the package and naming the plugin.
[Plugins](/guide/extension-points#plugins) in the extension points covers how to register one,
and how to write one.

Plugins work in hybrid and spec mode. Registering one needs swagger-php 7.3 or later, and
`Builder::withDiscoveredPlugins()` or `--discover-plugins`, which picks up a plugin the
package declares for discovery, needs 7.6 or later.

| Plugin                                                                  | Package                       | What it does                                                                               | Settings                                                                                  | Discovered |
| ----------------------------------------------------------------------- | ----------------------------- | ------------------------------------------------------------------------------------------ | ----------------------------------------------------------------------------------------- | ---------- |
| `Radebatz\OpenApi\Introspector\RoutesPlugin`                            | [openapi-introspector][1] 1.5 | Adds the routes a Laravel or Slim application already has as operations                    | An adapter class mapped to that adapter's settings, or adapters passed to the constructor | no         |
| `Radebatz\OpenApi\Introspector\LaravelDataPlugin`                       | [openapi-introspector][1] 1.5 | Describes spatie/laravel-data objects, and the data objects an operation takes and returns | none                                                                                      | yes        |
| `Radebatz\OpenApi\JsonSchema\JsonSchemaPlugin`                          | [openapi-json-schema][2] 0.2  | Describes plain classes through symfony/json-schema                                        | `direction`, `groups`, `qualifyAttributedClasses`, all optional                           | no         |
| `Radebatz\OpenApi\Verifier\Integrations\OpenApi\RecordedExamplesPlugin` | [openapi-verifier][3] 3.2     | Adds the responses recorded in tests as examples                                           | `directory`, required                                                                     | no         |

The package version is the first release with the plugin. Each package's own documentation
describes its settings.

A plugin that is not discovered is named in `withPlugins()`, with its settings:

```php
$builder->withPlugins([
    JsonSchemaPlugin::class => ['groups' => ['read']],
    RecordedExamplesPlugin::class => ['directory' => 'tests/examples'],
]);
```

Is a plugin missing? Create a pull request!

[1]: https://github.com/DerManoMann/openapi-introspector
[2]: https://github.com/DerManoMann/openapi-json-schema
[3]: https://github.com/DerManoMann/openapi-verifier
