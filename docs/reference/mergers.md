# Merger Reference

This page is generated automatically from the `swagger-php` sources.

For improvements head over to [GitHub](https://github.com/zircote/swagger-php) and create a PR ;)

Mergers decide when two attributes in one collection, such as two schemas or two operations,
are the same one, and what the one that survives looks like. `Augmenter\Merge` runs them over
the `Specification`'s collections, so each key reaches the compiler once. The
[Augmenters reference](/reference/augmenters) says when it runs.

Mergers are tried in registration order, and the first to claim a type handles it. The defaults
are listed in that order. `Merge\LastWins` claims every type, so a merger you add goes ahead of
it. The [extension points guide](/guide/extension-points#mergers) shows how to write and
register one.

## Default Mergers

### [Tags](https://github.com/zircote/swagger-php/tree/master/src/Merge/Tags.php)

Folds two tags with the same name field by field, instead of keeping one of them whole.

One of the Builder's default mergers, registered ahead of the catch-all: a tag is declared
once with its description and then named on every method or class that uses it, and each of
those is a declaration too.

What only one of them sets is always taken, so a tag that is only a name adds nothing and is
not reported. What both set differently is decided by the `Mode` it is constructed with,
`Mode::Last` by default, and reported. `NonOverlapping` keeps the earlier's value, as `First`
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

It claims every type, and keys each by the identity all shipped mergers share.

## Opt-in Mergers

### [Operations](https://github.com/zircote/swagger-php/tree/master/src/Merge/Operations.php)

Folds two operations for the same path and method field by field, instead of keeping one of
them whole.

Opt-in: it is not one of the Builder's default mergers. Register it ahead of the catch-all,
`withMergers(fn ($mergers) => $mergers->insert(new Merge\Operations(), Merge\LastWins::class))`,
when one operation is described in more than one place, such as a route table contributed
through `withSpecification()` and an attribute adding responses to it.

Identity is the shared one: path and method, or webhook and method. Registering the fold
changes how two operations combine, not which two are the same.

What only one of them sets is always taken. What both set is decided by the `Mode` it is
constructed with, `Mode::Last` by default: fold into the later, fold into the earlier, or
take only what does not overlap. The mode can also be a callable that receives both and returns
one, so the choice can follow a mark a producer left with `setMeta()` rather than the
order they arrive in. A field both set differently is reported, whichever value
is kept. One they set the same is not.

- Parameters are keyed by name and location, and responses by status code. Under `First` and
  `Last`, a parameter or response both describe is folded one level deep, a parameter's
  `schema` included, so a route's `pattern` survives an attribute's `type`. Under
  `NonOverlapping` it is the earlier's, whole.
- `requestBody`, `externalDocs`, `security`, `servers` and `callbacks` are each one decision,
  taken whole from whichever operation the mode keeps. An explicit `security: []` is set,
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

## Identity

What makes two attributes the same one depends on the collection they are in:

- anything in a `components` bucket: its component key
- an operation: its path and method, or its webhook and method
- a path item outside `components`: its path
- a tag: its name

Anything else has no identity and passes through. Servers and external documentation are
positional, so duplicates there are legal and stay.

## Modes

How a folding merger treats two attributes with the same identity where they overlap: a
field, or a keyed child such as a parameter, that both set.

What only one of them sets is always taken. A merger that accepts a mode takes either a case or a
callable that receives both and returns one, so the choice can depend on the pair, for
example on a mark one producer left with `setMeta()`.

### `Mode::First`

Fold into the earlier attribute: on a field both set differently, the earlier value stays.

### `Mode::Last`

Fold into the later attribute: on a field both set differently, the later value wins.

### `Mode::NonOverlapping`

Take only what one of them has alone: a field or child both set is left as the earlier
has it, and not folded into.
