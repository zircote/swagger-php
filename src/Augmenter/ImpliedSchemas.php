<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Contracts\TypeResolverAwareInterface;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Specification\ComponentKey;
use OpenApi\Type\SchemaType;
use OpenApi\Type\TypeResolver;
use OpenApi\Utils\PipeInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/**
 * Adds the schemas the document's types imply but nobody declared.
 *
 * A property, parameter or request body typed with a class that has no schema would lose its
 * type, since only a component can be referenced. Where the class can be described without
 * guessing, it is added as if it carried `#[OA\Schema]`, and the augmenters after this one name,
 * fill and reference it as they would a declared schema.
 *
 * Today that is a PHP enum, whose cases are the whole description: `Names` names it, `Enums` fills
 * its cases and `Types` references it. A backed enum's schema takes the backing type, so it lists
 * the backing values, which is what a backed enum serialises to. A bare `#[OA\Schema]` on the enum
 * lists the case names instead.
 *
 * @implements PipeInterface<Specification>
 */
class ImpliedSchemas implements PipeInterface, LoggerAwareInterface, TypeResolverAwareInterface
{
    protected ?LoggerInterface $logger = null;

    /** @var array<class-string<\UnitEnum>, true> */
    protected array $enums = [];

    public function __construct(
        protected TypeResolver $typeResolver = new TypeResolver(),
    ) {
    }

    public function __invoke(mixed $payload): mixed
    {
        $this->enums = [];

        $payload->getWalker()->eachTypedAttribute(function (OA\Property|OA\Parameter|OA\RequestBody $attribute): void {
            $this->collect($attribute->getReflector(), $attribute instanceof OA\RequestBody ? null : $attribute->schema);
        });

        $index = $payload->buildComponentIndex();
        foreach (array_keys($this->enums) as $enum) {
            if ($index->hasKey(new ComponentKey($enum))) {
                continue;
            }

            $reflector = new \ReflectionEnum($enum);
            $type = match ($reflector->getBackingType()?->getName()) {
                'int' => 'integer',
                'string' => 'string',
                default => null,
            };
            $schema = new OA\Schema(type: $type);
            $schema->setReflector($reflector);
            $payload->add($schema);
        }

        return null;
    }

    public function setTypeResolver(TypeResolver $typeResolver): static
    {
        $this->typeResolver = $typeResolver;
        if ($this->logger instanceof LoggerInterface) {
            $this->typeResolver->setLogger($this->logger);
        }

        return $this;
    }

    /**
     * Passes the logger to the type resolver, including one set later.
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
        $this->typeResolver->setLogger($logger);
    }

    public function group(): string|\BackedEnum
    {
        return Group::Resolve;
    }

    /**
     * The enums in a member's type, unless its schema references something or lists its own
     * `enum`, which `Types` keeps in place of a reference.
     */
    protected function collect(?\Reflector $reflector, ?OA\Schema $schema = null): void
    {
        if ($schema instanceof OA\Schema && ($schema->ref !== null || $schema->enum !== null)) {
            return;
        }

        if ($reflector instanceof \ReflectionProperty || $reflector instanceof \ReflectionParameter || $reflector instanceof \ReflectionMethod) {
            $this->collectFromType($this->typeResolver->resolve($reflector));
        }
    }

    protected function collectFromType(?SchemaType $type): void
    {
        if (!$type instanceof SchemaType) {
            return;
        }

        $class = $type->isRef() && is_string($type->type) ? ltrim($type->type, '\\') : null;
        if ($class !== null && is_a($class, \UnitEnum::class, true)) {
            $this->enums[$class] = true;
        }

        $this->collectFromType($type->items);
        $this->collectFromType($type->additionalProperties instanceof SchemaType ? $type->additionalProperties : null);
        foreach ([...$type->oneOf ?? [], ...$type->allOf ?? [], ...$type->anyOf ?? []] as $member) {
            $this->collectFromType($member);
        }
    }
}
