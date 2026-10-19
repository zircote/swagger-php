Mergers decide when two attributes in one collection, such as two schemas or two operations,
are the same one, and what the one that survives looks like. `Augmenter\Merge` runs them over
the `Specification`'s collections, so each key reaches the compiler once. The
[Augmenters reference](/reference/augmenters) says when it runs.

Mergers are tried in registration order, and the first to claim a type handles it. The defaults
are listed in that order. `Merge\LastWins` claims every type, so a merger you add goes ahead of
it. The [extension points guide](/guide/extension-points#mergers) shows how to write and
register one.
