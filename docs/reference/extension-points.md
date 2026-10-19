# Extension Point Reference

This page is generated automatically from the `swagger-php` sources.

For improvements head over to [GitHub](https://github.com/zircote/swagger-php) and create a PR ;)

What the assembler, the attribute factory and the resolver run by default at each extension
point, in the order they run. For adding your own, see
[Extension points](/guide/extension-points).

[Augmenters](/reference/augmenters), [mergers](/reference/mergers) and
[compilers](/reference/architecture#compilers) have their own listings.

## Default Translators

### [DefaultAttributeTranslator](https://github.com/zircote/swagger-php/tree/master/src/Assembler/DefaultAttributeTranslator.php)

Default implementation handling native (OpenApi) attributes.

### [OptionalPropertyAttributeTranslator](https://github.com/zircote/swagger-php/tree/master/src/Assembler/OptionalPropertyAttributeTranslator.php)

Add the required `OA\Property` on schema properties that only have:
- an `OA\Schema`
- an `OA\Encoding`

This is what implements the implicit `OA\Property` shortcut. The `OA\MediaType` shortcuts
are the `Augmenter\Shortcuts` augmenter; the `OA\Parameter` ones are plain subclasses.

Not in a class carrying `OA\Components`: there a schema on a property is a component of its
own, named after the property, so it is left for the holder to absorb.

### [OperationMethodsTranslator](https://github.com/zircote/swagger-php/tree/master/src/Assembler/OperationMethodsTranslator.php)

Split an `OA\Operation` declaring `methods:` into one operation per method.

The first method keeps the declared operation, every other one gets a `copy()` of it. When
the split operation is the only operation on its method, what would nest into it is set to
`Fan::All` where it leaves `fan` unset: its siblings, and the attributes on the method's PHP
parameters. So each method gets the whole description. Next to other operations nothing is
fanned, and an attribute that could go into more than one of them is the usual ambiguity.

An explicit `operationId` on several methods has to tell them apart with `{method}`, which
`Augmenter\OperationIds` expands. Without it, it is dropped with a warning and each method
gets a generated one.

A translator creating `methods:` operations itself has to run before this one: `insert()` it
into the translators ahead of this class rather than `add()` it.

## Default Resolvers

### [Reflection](https://github.com/zircote/swagger-php/tree/master/src/Resolver/Reflection.php)

Resolves missing components by collecting the referenced class with the assembler in use.

This makes listing all related classes as builder sources optional; adding a single controller
is enough as long as everything it references (directly or transitively) carries spec attributes.

A FQCN is considered resolved if the specification knows it once collected. Classes without any
spec attributes are left to the next resolver in the chain.
