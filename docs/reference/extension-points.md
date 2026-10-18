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

## Default Mergers

### [Tags](https://github.com/zircote/swagger-php/tree/master/src/Merge/Tags.php)

Folds two tags with the same name field by field, instead of keeping one of them whole.

One of the Builder's default mergers, registered ahead of the catch-all: a tag is declared
once with its description and then named on every method or class that uses it, and each of
those is a declaration too.

What only one of them sets is always taken, so a tag that is only a name adds nothing and is
not reported. What both set differently is decided by the `Mode` it is constructed with,
`Mode::Last` by default, and reported; `NonOverlapping` keeps the earlier's value, as `First`
does. `attachables` is a union, and `x` merges key by key under the same mode.

The survivor is a new tag, a copy of the earlier with the later folded in. It keeps the
earlier's reflector, source location and `meta`.

#### Config settings
- **mode** : `OpenApi\Merge\Mode|callable` · default: `Mode::Last`  
  How two attributes with the same identity are treated where they overlap: a `Mode`, or a
  callable that receives the earlier and the later and returns one.

### [LastWins](https://github.com/zircote/swagger-php/tree/master/src/Merge/LastWins.php)

The catch-all merger: one entry per key survives, the later one, and the author is told.

It reduces rather than folds: two entries in, one out, so the compiler never sees a collision
and its own accidental rules stop deciding anything. Combining fields from both attributes is a
type-specific merger's job, registered ahead of this one.

Identity by collection: the component key for anything in a `components` bucket, path and
method for an operation, webhook and method for a webhook operation, path for a path-bound
path item, name for a tag. Servers, security requirements and external documentation are
positional — their entries have no identity, duplicates are legal, and they pass through.

## Opt-in Mergers

### [Operations](https://github.com/zircote/swagger-php/tree/master/src/Merge/Operations.php)

Folds two operations for the same path and method field by field, instead of keeping one of
them whole.

Opt-in: it is not one of the Builder's default mergers. Register it ahead of the catch-all,
`withMergers(fn ($mergers) => $mergers->insert(new Merge\Operations(), Merge\LastWins::class))`,
when one operation is described in more than one place, such as a route table contributed
through `withSpecification()` and an attribute adding responses to it.

Identity is the catch-all's: path and method, or webhook and method. Registering the fold
changes how two operations combine, not which two are the same.

What only one of them sets is always taken. What both set is decided by the `Mode` it is
constructed with, `Mode::Last` by default: fold into the later, fold into the earlier, or
take only what does not overlap. The mode can also be a callable that receives both and returns
one, so the choice can follow a mark a producer left with `setMeta()` rather than the
order they arrive in. A field both set differently is reported, whichever value
is kept; one they set the same is not.

- Parameters are keyed by name and location, and responses by status code. Under `First` and
  `Last`, a parameter or response both describe is folded one level deep, a parameter's
  `schema` included, so a route's `pattern` survives an attribute's `type`. Under
  `NonOverlapping` it is the earlier's, whole.
- `requestBody`, `externalDocs`, `security`, `servers` and `callbacks` are each one decision,
  taken whole from whichever operation the mode keeps; an explicit `security: []` is set,
  and opts out.
- `tags` and `attachables` are unions, and `x` merges key by key under the same mode.

The survivor is a new operation, a shallow copy of the earlier with the later folded in, so
neither operation is changed. What it takes whole, such as a response or a request body, is
the contribution's own object, and the augmenters that run later change it in place, as they
would without the merge. It keeps the earlier's reflector, source location and `meta`,
falling back to the later's reflector when the earlier has none.

#### Config settings
- **mode** : `OpenApi\Merge\Mode|callable` · default: `Mode::Last`  
  How two attributes with the same identity are treated where they overlap: a `Mode`, or a
  callable that receives the earlier and the later and returns one.
