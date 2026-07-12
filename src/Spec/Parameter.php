<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

use OpenApi\Undefined;

/**
 * Describes a single operation parameter.
 *
 * Typed subtypes pre-fill `in` (and `required` for path):
 * - `OA\Parameter\Path` - path parameters (in: path, required: true)
 * - `OA\Parameter\Query` - query string parameters (in: query)
 * - `OA\Parameter\Header` - header parameters (in: header)
 * - `OA\Parameter\Cookie` - cookie parameters (in: cookie)
 *
 * Inline on an operation:
 *
 *   #[OA\Operation\Get(path: '/pets', parameters: [
 *       new OA\Parameter\Query(name: 'status', schema: new OA\Schema(type: 'string', enum: ['active', 'sold'])),
 *   ])]
 *
 * Or as a reusable component (set `parameter` for the component key):
 *
 *   #[OA\Parameter\Path(parameter: 'petId', name: 'id', schema: new OA\Schema(type: 'integer'))]
 *
 * Produces:
 *   components:
 *     parameters:
 *       petId:
 *         name: id
 *         in: path
 *         required: true
 *         schema:
 *           type: integer
 *
 * @see [Parameter Object](https://spec.openapis.org/oas/v3.1.1.html#parameter-object)
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER | \Attribute::IS_REPEATABLE)]
class Parameter extends AbstractAttribute
{
    /**
     * @param string|null              $parameter       Reusable parameter identifier (component key)
     * @param string|null              $name            The name of the parameter
     * @param string|null              $in              The location of the parameter (query, header, path, cookie)
     * @param string|null              $description     A brief description of the parameter (CommonMark syntax)
     * @param bool|null                $required        Whether the parameter is mandatory
     * @param bool|null                $deprecated      Whether the parameter is deprecated
     * @param bool|null                $allowEmptyValue Whether empty-valued parameters are allowed
     * @param string|null              $ref             A JSON Reference to a reusable parameter
     * @param string|null              $style           How the parameter value is serialized
     * @param bool|null                $explode         Whether arrays/objects generate separate parameters
     * @param bool|null                $allowReserved   Whether reserved characters are allowed without encoding
     * @param Schema|null              $schema          The schema defining the type for the parameter
     * @param mixed                    $example         Example of the parameter's value
     * @param list<Example>|null       $examples        Examples of the parameter's value
     * @param list<MediaType>|null     $content         Content-type based parameter serialization
     * @param array<string,mixed>|null $x               Vendor extensions (x-* properties)
     */
    public function __construct(
        public ?string $parameter = null,
        public ?string $name = null,
        public ?string $in = null,
        public ?string $description = null,
        public ?bool $required = null,
        public ?bool $deprecated = null,
        public ?bool $allowEmptyValue = null,
        public ?string $ref = null,
        public ?string $style = null,
        public ?bool $explode = null,
        public ?bool $allowReserved = null,
        public ?Schema $schema = null,
        public mixed $example = Undefined::UNDEFINED,
        public ?array $examples = null,
        public ?array $content = null,
        ?array $x = null,
    ) {
        parent::__construct(x: $x);
    }

    public function isRoot(): bool
    {
        return $this->ref === null && $this->parameter !== null;
    }

    public function merge(): array
    {
        return [
            Operation::class => 'parameters[]',
            PathItem::class => 'parameters[]',
        ];
    }

    public function contains(): array
    {
        return [
            MediaType::class => 'content[]',
            Example::class => 'examples[]',
        ];
    }
}
