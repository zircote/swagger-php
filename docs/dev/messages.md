# Writing messages

How the spec pipeline words its warnings, errors and exceptions, and which level each one takes.
This covers `Builder`, `Resolver`, the `Assembler`, the augmenters, the merge strategies, the
compilers, plugins and the `Spec` attributes. Classic keeps its own messages until it is removed
in 8.0.

## Form

1. **Subject first**, named as a user would search for it: `Schema "Pet"`, `Property
   Pet::$name`, `Operation "getPets"`. No internal class or processor names such as `Ref:`.
2. **What is wrong, then what happens**: `…, omitted` or `…, using enum fallback`, with one
   phrasing per outcome.
3. **Location last**, ` in <source>`, wherever it is known.
4. **User values in double quotes**, spec keywords bare (`const`, `paths`).
5. **Sentence case**, no trailing period, no em dash.

## Levels

The level is behaviour, not tone. An `error` makes `Result::isValid()` false and the CLI exit
with 1.

| Level | When | For example |
|---|---|---|
| `error` | The document is invalid | a required field missing, a duplicate `operationId`, a reference that resolves to nothing |
| `error` | Something declared is lost | a class or member skipped, a component declared twice |
| `error` | The configuration is wrong | an unknown config option or key |
| `warning` | The output differs from what was declared | `const` written as `enum` in 3.0, a docblock type falling back to the type hint, two operations folded by the merge strategy, a 3.1 feature left out of a 3.0 document |
| `warning` | The document is valid, but likely not what was meant | an array without `items` in 3.1, a request body on `GET` |
| `notice` | A deprecated form still works | a classic type alias |
| `notice`, `debug` | Information | the components cleanup removed |

A merge collision is a `warning` although one declaration loses: the merge strategy that decides
it is the user's choice. So is what a compiler leaves out because the target version cannot
express it, since one source may be built for several versions. What a compiler keeps although the
version does not know it makes the document invalid, and is an `error`.

Exceptions are for code calling the API wrongly. The content being documented never throws: a
class, member or attribute that cannot be read is skipped with an `error`, and the build goes on.
