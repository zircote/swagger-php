# Vendor extensions

The specification allows for [custom properties](http://swagger.io/specification/#vendorExtensions)
as long as they start with "x-". Therefore, all swagger-php attributes and annotations have an `x` property which accepts an array (map)
and will unfold into "x-" properties.

<codeblock id="custom-property">
  <template v-slot:at>

<<< @/snippets/guide/vendor-extensions/custom_property_at.php

  </template>
  <template v-slot:an>

<<< @/snippets/guide/vendor-extensions/custom_property_an.php

  </template>
  <template v-slot:spec>

<<< @/snippets/guide/vendor-extensions/custom_property_spec.php

  </template>
</codeblock>

**Results in:**

```yaml
openapi: 3.1.0
info:
  title: Example
  version: 1
  x-some-name: a-value
  x-another: 2
  x-complex-type:
    supported:
      - version: "1.0"
        level: baseapi
      - version: "2.1"
        level: fullapi
```

Two attributes differ. A property has no object of its own in the document, so the `x` of a
`Property` goes on the property's schema. A Security Requirement Object cannot carry extensions,
so the `x` of a `Security\Requirement` is not written. It is reported with a warning, and the
parameter is removed in 8.0.
