<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Undefined;
use OpenApi\Utils\PipeInterface;

/**
 * Resolves shortcut attributes into the plain attributes they stand for.
 *
 * A shortcut carries schema keywords on an attribute that holds a schema, so the common case
 * needs no nested `OA\Schema`. This augmenter moves those keywords to where the document has
 * them.
 *
 * Handles:
 * * `OA\MediaType\Json` and `OA\MediaType\Xml`: their schema keywords move into the media
 *   type's `schema`;
 * * `OA\Schema\Items`: an array schema, whose item keywords move into a new `items` schema.
 *
 * @implements PipeInterface<Specification>
 */
class Shortcuts implements PipeInterface
{
    /** What stays on the array schema an `OA\Schema\Items` becomes, besides the array keywords. */
    private const ITEMS_KEEP_PROPERTIES = ['schema', 'component', 'title', 'description', 'deprecated', 'readOnly', 'writeOnly', 'xml', 'externalDocs', 'x', 'attachables'];

    public function __invoke(mixed $payload): mixed
    {
        $this->processMediaTypes($payload);
        $this->processSchemaItems($payload);

        return null;
    }

    public function group(): string|\BackedEnum
    {
        return Group::Resolve;
    }

    /**
     * Every `Json` or `Xml` media type in a `content` list, wherever it is nested.
     */
    protected function processMediaTypes(Specification $specification): void
    {
        $specification->getWalker()->visit(AttributeInterface::class, function (AttributeInterface $attribute): void {
            if (property_exists($attribute, 'content') && $attribute->content !== null && is_array($attribute->content)) {
                foreach ($attribute->content as $mediaType) {
                    if ($mediaType instanceof OA\MediaType\Json || $mediaType instanceof OA\MediaType\Xml) {
                        $this->processMediaType($mediaType);
                    }
                }
            }
        });
    }

    /**
     * Move the media type's schema keywords (`ref`, `type`, `items`, `properties`, `required`)
     * into its `schema`, creating one when there is none. With a `schema` already set, a keyword
     * moves only where the schema has no value of its own.
     */
    protected function processMediaType(OA\MediaType\Json|OA\MediaType\Xml $mediaType): void
    {
        // spelled out per property rather than looped over a list of property names: the
        // shortcut properties mirror OA\Schema's one for one, but a dynamic
        // `$x->{$prop} = $y->{$prop}` makes that invisible — phpstan can only see the union
        // of all five types on each side, so every assignment looks like a type error
        if (!$mediaType->schema instanceof OA\Schema) {
            $mediaType->schema = new OA\Schema(
                ref: $mediaType->ref,
                type: $mediaType->type,
                items: $mediaType->items,
                properties: $mediaType->properties,
                required: $mediaType->required,
            );
            $mediaType->ref = null;
            $mediaType->type = null;
            $mediaType->items = null;
            $mediaType->properties = null;
            $mediaType->required = null;

            return;
        }

        // only a shortcut the schema has no value for is moved across; one the schema already
        // sets is left on the media type untouched, as it was before
        $schema = $mediaType->schema;
        if ($mediaType->ref !== null && $schema->ref === null) {
            $schema->ref = $mediaType->ref;
            $mediaType->ref = null;
        }
        if ($mediaType->type !== null && $schema->type === null) {
            $schema->type = $mediaType->type;
            $mediaType->type = null;
        }
        if ($mediaType->items !== null && $schema->items === null) {
            $schema->items = $mediaType->items;
            $mediaType->items = null;
        }
        if ($mediaType->properties !== null && $schema->properties === null) {
            $schema->properties = $mediaType->properties;
            $mediaType->properties = null;
        }
        if ($mediaType->required !== null && $schema->required === null) {
            $schema->required = $mediaType->required;
            $mediaType->required = null;
        }
    }

    /**
     * Every `OA\Schema\Items` in the specification, wherever it is nested, except one held directly
     * in the `items` of a schema that is not an `Items` itself.
     *
     * There it is the item schema, not an array of them: `new OA\Schema(type: 'array', items: new
     * OA\Schema\Items(type: 'string'))` is an array of strings. In the `items` of another `Items`
     * it is an array, so an array of arrays.
     */
    protected function processSchemaItems(Specification $specification): void
    {
        $walker = $specification->getWalker();

        $itemSchemas = new \SplObjectStorage();
        $walker->eachSchema(static function (OA\Schema $schema) use ($itemSchemas): void {
            if (!$schema instanceof OA\Schema\Items && $schema->items instanceof OA\Schema\Items) {
                $itemSchemas->offsetSet($schema->items);
            }
        });

        // the walker visits children before their parent, so a nested `Items` is resolved first
        $walker->visit(OA\Schema\Items::class, function (OA\Schema\Items $items) use ($itemSchemas): void {
            if (!$itemSchemas->offsetExists($items)) {
                $this->processSchemaItem($items);
            }
        });
    }

    /**
     * Turn an `OA\Schema\Items` into the array schema it stands for, in place.
     *
     * `#[OA\Schema\Items(ref: Tag::class, minItems: 1)]` describes an array of `Tag`. The array
     * keywords and the metadata in `ITEMS_KEEP_PROPERTIES` stay on the attribute, which becomes
     * `type: array`. Every other `OA\Schema` keyword it sets moves into a new `items` schema.
     * With nothing to move and no `items`, the items schema is empty: an array of anything. It
     * does not recurse: an `Items` in `items` is resolved by the caller, before this one.
     */
    protected function processSchemaItem(OA\Schema\Items $items): void
    {
        $itemKeep = [...OA\Schema::ARRAY_PROPERTIES, ...self::ITEMS_KEEP_PROPERTIES];

        // the keywords to move are read off `OA\Schema`'s constructor, so a keyword added there
        // moves without being listed here. Each one becomes an argument for the new items schema
        // and is cleared on the array.
        $innerArgs = [];
        $schema = new \ReflectionClass(OA\Schema::class);
        foreach ($schema->getConstructor()->getParameters() as $param) {
            $prop = $param->getName();
            // public properties only: the constructor also takes arguments that are not schema
            // keywords and have no public property of the same name. `fan` is one: it says where
            // the attribute nests, belongs to the array, and is not a keyword the items would carry.
            if (in_array($prop, $itemKeep, true) || !$schema->hasProperty($prop) || !$schema->getProperty($prop)->isPublic()) {
                continue;
            }
            $value = $items->{$prop};
            if ($value !== null && $value !== Undefined::UNDEFINED) {
                $innerArgs[$prop] = $value;
                $items->{$prop} = null;
            }
        }

        $items->type = 'array';
        if ($innerArgs || $items->items === null) {
            $items->items = new OA\Schema(...$innerArgs);
        }
    }
}
