---
layout: home
hero:
  name: Swagger-PHP
  tagline: Generate OpenAPI documentation for your RESTful API.
  actions:
      - theme: brand
        text:  User Guide →
        link: /guide/
features:
  - title: OpenAPI conformant
    details: Generate OpenAPI documents in version 3.0, 3.1 or 3.2.
  - title: Document your API inside PHP source code
    details: Using swagger-php lets you write the API documentation inside the PHP source files
      which helps keeping the documentation up-to-date.
  - title: Typed attributes
    details: Attributes are PHP objects that can also be built by hand, and the pipeline reports
      when one is wrong.
---

### 1. Install with composer:

```shell
> composer require zircote/swagger-php
```

### 2. Update your code

Add `swagger-php` [attributes](guide/spec-attributes) to your source code.

<codeblock id="minimal">
  <template v-slot:spec>

<<< @/snippets/minimal_api_spec.php

  </template>
  <template v-slot:at>

<<< @/snippets/minimal_api_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/minimal_api_an.php

  </template>
</codeblock>

### 3. Generate OpenAPI documentation

```shell
> ./vendor/bin/openapi src -o openapi.yaml
```

### 4. Explore and interact with your API

Use an OpenAPI tool like [Swagger UI](https://swagger.io/tools/swagger-ui/) or [Scalar](https://github.com/scalar/scalar) to explore and interact with your API.

## Links

- [User Guide](guide/index.md)
- [Reference](reference/index.md)
- [OpenApi Documentation](https://learn.openapis.org/)
- [OpenApi Specification](https://spec.openapis.org/oas/v3.1.0.html)
- [Learn by example](https://github.com/zircote/swagger-php/tree/master/docs/examples#readme)
- [Related projects](related-projects.md)
- [swagger-php 2.x documentation](https://github.com/zircote/swagger-php/tree/2.x/docs)
- [swagger-php 3.x documentation](https://github.com/zircote/swagger-php/tree/3.x/docs)
