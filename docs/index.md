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
    details: Generate OpenAPI documents in version 3.0 or 3.1.
  - title: Document your API inside PHP source code
    details: Using swagger-php lets you write the API documentation inside the PHP source files
      which helps keeping the documentation up-to-date.
  - title: Annotation and Attribute support
    details: Annotations can be either docblocks or PHP 8.1 attributes.
---

### 1. Install with composer:

```shell
> composer require zircote/swagger-php
```

### 2. Update your code

Add `swagger-php` attributes (or legacy annotations) to your source code.

⚠️ `doctrine/annotations` is going to be deprecated in the future, so wherever
possible attributes should be used.

<codeblock id="minimal">
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

Use an OpenAPI tool like [Swagger UI](https://swagger.io/tools/swagger-ui/) to explore and interact with your API.

## Links

- [User Guide](guide/index.md)
- [Reference](reference/index.md)
- [OpenApi Documentation](https://learn.openapis.org/)
- [OpenApi Specification](https://spec.openapis.org/oas/v3.1.0.html)
- [Learn by example](https://github.com/zircote/swagger-php/tree/master/docs/examples#readme)
- [Related projects](related-projects.md)
- [swagger-php 2.x documentation](https://github.com/zircote/swagger-php/tree/2.x/docs)
- [swagger-php 3.x documentation](https://github.com/zircote/swagger-php/tree/3.x/docs)
