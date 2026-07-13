
Augmenters enrich the collected specification with inferred data before compilation.
They run in three groups — **resolve** (type inference, refs), **reduce** (filtering, cleanup),
and **augment** (docblocks, operation ids, tags) — and are listed below in execution order.

Augmenters are part of the spec-attributes pipeline (`--mode spec` or `--mode hybrid`).
