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

## Opt-in Mergers

### [Operations](https://github.com/zircote/swagger-php/tree/master/src/Merge/Operations.php)

Folds two halves of one operation field by field, instead of keeping one of them whole.

Opt-in: it is not one of the Builder's default mergers. Register it ahead of the catch-all,
`withMergers(fn ($mergers) => $mergers->insert(new Merge\Operations(), Merge\LastWins::class))`,
when one operation is described in more than one place, such as a route table contributed
through `withSpecification()` and an attribute adding responses to it.

Identity is the catch-all's: path and method, or webhook and method. Registering the fold
changes how two operations combine, not which two are the same.

What only one half sets is always taken. What both set is decided by the `Mode` it is
constructed with, `Mode::Last` by default: fold into the later half, fold into the earlier, or
take only what does not overlap. The mode can also be a callable that receives both halves and
returns one, so the choice can follow a mark a producer left with `setMeta()` rather than the
order the halves arrive in. A field both halves set differently is reported, whichever value
is kept; one they set the same is not.

- Parameters are keyed by name and location, and responses by status code. Under `First` and
  `Last`, a parameter or response both describe is folded one level deep, a parameter's
  `schema` included, so a route's `pattern` survives an attribute's `type`. Under
  `NonOverlapping` it is the earlier half's, whole.
- `requestBody`, `externalDocs`, `security`, `servers` and `callbacks` are each one decision,
  taken whole from whichever half the mode keeps; an explicit `security: []` is set, and opts
  out.
- `tags` and `attachables` are unions, and `x` merges key by key under the same mode.

The survivor is a new operation, a copy of the earlier half with the later folded in, so
neither contribution is changed. It keeps the earlier half's reflector, source location and
`meta`, falling back to the later half's reflector when the earlier has none.

#### Config settings
- **mode** : `OpenApi\Merge\Mode|callable` · default: `Mode::Last`  
  How two halves that overlap are treated: a `Mode`, or a callable that receives the earlier
  and the later half and returns one.
