<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec\MediaType;

use OpenApi\Spec as OA;
use OpenApi\Spec\Concerns\SchemaInputTrait;
use OpenApi\Spec\Property;
use OpenApi\Spec\Schema;
use OpenApi\Spec\Type;
use OpenApi\Undefined;

/**
 * Describes the content payload for `application/json`.
 *
 * A shortcut version of `OA\MediaType` with some of the more common `OA\Schema` properties added.
 * * `mediaType` is set to `application/json` by default.
 * * `ref`, `type`, `items`, `properties` and `required` may be used and will be expanded into a nested `OA\Schema` automatically.
 * * If `schema` is explicitly set, the custom `OA\Schema` properties will be ignored.
 *
 * Allows to shorten this:
 *
 *   #[OA\Response(response: 200, content: [
 *       new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(type: 'array', items: new OA\Schema(ref: Pet::class))),
 *   ])]
 *
 * to this:
 *
 *   #[OA\Response(response: 200, content: [new OA\MediaType\Json(type: 'array', items: new OA\Schema(ref: Pet::class))])]
 *
 * The `Shortcuts` augmenter expands the schema properties into a nested `OA\Schema` automatically.
 *
 * @see [Media Type Object](https://spec.openapis.org/oas/v3.1.1.html#media-type-object)
 *
 * @phpstan-import-type TypeInput from Schema
 * @phpstan-import-type PropertiesInput from Schema
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Json extends OA\MediaType
{
    use SchemaInputTrait;

    /** @var string|list<string>|null */
    public string|array|null $type = null;

    /** @var list<Property>|null */
    public ?array $properties = null;

    /**
     * @param string|null                                      $ref         A JSON Reference to a reusable schema
     * @param TypeInput|null                                   $type        The value type(s) (string, number, integer, boolean, array, object, null)
     * @param Schema|string|null                               $items       Schema for array items
     * @param PropertiesInput|null                             $properties  Object property definitions, as a list of `Property` or keyed by property name
     * @param list<string>|null                                $required    List of required property names
     * @param Schema|null                                      $schema      The schema defining the content
     * @param mixed                                            $example     Example of the media type content
     * @param list<OA\Example>|null                            $examples    Examples of the media type content
     * @param list<OA\Encoding>|array<string,OA\Encoding>|null $encoding    Encoding information for specific properties
     * @param string|null                                      $component   The key this is filed under in `components`, which makes it a reusable component
     * @param array<string,mixed>|null                         $x           Vendor extensions (x-* properties)
     * @param list<OA\Attachable>|null                         $attachables Reusable custom attachable attributes
     */
    public function __construct(
        // schema shortcuts
        public string|Schema\Ref|null $ref = null,
        string|Type|array|null $type = null,
        public Schema|string|null $items = null,
        ?array $properties = null,
        public ?array $required = null,
        // media type
        ?Schema $schema = null,
        mixed $example = Undefined::UNDEFINED,
        ?array $examples = null,
        ?array $encoding = null,
        ?string $component = null,
        ?array $x = null,
        ?array $attachables = null,
    ) {
        parent::__construct(
            mediaType: 'application/json',
            schema: $schema,
            example: $example,
            examples: $examples,
            encoding: $encoding,
            component: $component,
            x: $x,
            attachables: $attachables
        );
        $this->type = self::typeValue($type);
        $this->properties = self::propertyList($properties);
    }
}
