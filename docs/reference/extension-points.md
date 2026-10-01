# Extension Point Reference

This page is generated automatically from the `swagger-php` sources.

For improvements head over to [GitHub](https://github.com/zircote/swagger-php) and create a PR ;)

What the assembler, the attribute factory and the resolver run by default at each extension
point, in the order they run. For adding your own, see
[Extension points](/guide/extension-points).

[Augmenters](/reference/augmenters) and [compilers](/reference/architecture#compilers) have
their own listings.

## Default Translators

### [DefaultAttributeTranslator](https://github.com/zircote/swagger-php/tree/master/src/Assembler/DefaultAttributeTranslator.php)

Default implementation handling native (OpenApi) attributes.

### [OptionalPropertyAttributeTranslator](https://github.com/zircote/swagger-php/tree/master/src/Assembler/OptionalPropertyAttributeTranslator.php)

Add the required `OA\Property` on schema properties that only have:
- an `OA\Schema`
- an `OA\Encoding`

This is what implements the implicit `OA\Property` shortcut. The `OA\MediaType` shortcuts
are the `Augmenter\Shortcuts` augmenter; the `OA\Parameter` ones are plain subclasses.

## Default Resolvers

### [Reflection](https://github.com/zircote/swagger-php/tree/master/src/Resolver/Reflection.php)

Resolves missing components by collecting the referenced class with the assembler in use.

This makes listing all related classes as builder sources optional; adding a single controller
is enough as long as everything it references (directly or transitively) carries spec attributes.

A FQCN is considered resolved if the specification knows it once collected. Classes without any
spec attributes are left to the next resolver in the chain.

## Default Mergers

### [LastWins](https://github.com/zircote/swagger-php/tree/master/src/Merge/LastWins.php)

The catch-all merger: one entry per key survives, the later one, and the author is told.

It reduces rather than folds: two entries in, one out, so the compiler never sees a collision
and its own accidental rules stop deciding anything. Combining fields from both halves is a
type-specific merger's job, registered ahead of this one.

Identity by collection: the component key for anything in a `components` bucket, path and
method for an operation, webhook and method for a webhook operation, path for a path-bound
path item, name for a tag. Servers, security requirements and external documentation are
positional — their entries have no identity, duplicates are legal, and they pass through.
