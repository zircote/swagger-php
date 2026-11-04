<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Specification;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Contracts\RefInterface;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Utils\JsonPointer;

/**
 * Traversal helpers for walking the Specification tree.
 */
class Walker
{
    public function __construct(
        protected readonly Specification $specification,
    ) {
    }

    /**
     * Walk every Schema in the specification, recursively into nested schemas.
     *
     * @param callable(OA\Schema): void $visitor
     */
    public function eachSchema(callable $visitor): void
    {
        $this->visit(OA\Schema::class, $visitor);
    }

    /**
     * Walk every attribute whose schema can be inferred from its reflector's PHP type: each
     * property in a schema's `properties`, with that schema, and each parameter and request body.
     * These are the attributes `Augmenter\Types` types.
     *
     * Whether the reflector carries a type, and whether the attribute's schema leaves room for
     * one, is the visitor's to decide.
     *
     * @param callable(OA\Property|OA\Parameter|OA\RequestBody, ?OA\Schema): void $visitor
     */
    public function eachTypedAttribute(callable $visitor): void
    {
        // one walk for all three kinds; each walk visits the whole document
        $this->visit(AttributeInterface::class, static function (AttributeInterface $attribute) use ($visitor): void {
            if ($attribute instanceof OA\Schema) {
                foreach ($attribute->properties ?? [] as $property) {
                    $visitor($property, $attribute);
                }
            } elseif ($attribute instanceof OA\Parameter || $attribute instanceof OA\RequestBody) {
                $visitor($attribute, null);
            }
        });
    }

    /**
     * Walk every attribute in the specification that has a ref.
     *
     * @param callable(RefInterface): void $visitor
     */
    public function eachRef(callable $visitor): void
    {
        $this->visit(AttributeInterface::class, function (AttributeInterface $attribute) use ($visitor): void {
            if ($attribute instanceof RefInterface && $attribute->getRefValue() !== null) {
                $visitor($attribute);
            }

            // special case
            if ($attribute instanceof OA\Schema) {
                if ($attribute->discriminator instanceof OA\Discriminator && $attribute->discriminator->mapping !== null) {
                    foreach ($attribute->discriminator->mapping as $ref) {
                        $visitor(new OA\Schema(ref: $ref));
                    }
                }
            }

            // special case
            if ($attribute instanceof OA\Security\Requirement) {
                foreach (array_keys($attribute->toArray()) as $schemeName) {
                    $visitor(new OA\Security\Scheme(ref: JsonPointer::ref('components', 'securitySchemes', $schemeName)));
                }
            }
        });
    }

    /**
     * Walk every attribute that is an instance of `$visitee`, or of any class in it.
     *
     * Each call walks the whole specification, so a pass after several kinds of attribute
     * names them all in one call. They arrive in walk order, not grouped by class.
     *
     * @template T of AttributeInterface
     *
     * @param class-string<T>|list<class-string<T>> $visitee
     * @param callable(T): void                     $visitor
     */
    public function visit(string|array $visitee, callable $visitor): void
    {
        // one `$seen` for every bucket: an attribute reachable from two of them — an
        // operation held both directly and inside a path item — is still one attribute
        $seen = new \SplObjectStorage();

        foreach (get_object_vars($this->specification) as $buckets) {
            $this->walk($visitee, $visitor, $buckets instanceof AttributeInterface ? [$buckets] : (array) $buckets, $seen);
        }
    }

    /**
     * @template T of AttributeInterface
     *
     * @param class-string<T>|list<class-string<T>> $visitee
     * @param callable(T): void                     $visitor
     * @param array<mixed>                          $candidates
     * @param \SplObjectStorage<object, mixed>|null $seen
     */
    protected function walk(string|array $visitee, callable $visitor, array $candidates, ?\SplObjectStorage $seen = null): void
    {
        $seen ??= new \SplObjectStorage();

        foreach ($candidates as $candidate) {
            if ($candidate instanceof AttributeInterface) {
                if ($seen->offsetExists($candidate)) {
                    continue;
                }
                $seen->offsetSet($candidate);

                foreach (get_object_vars($candidate) as $property) {
                    if ($property instanceof AttributeInterface) {
                        $this->walk($visitee, $visitor, [$property], $seen);
                    } elseif (is_array($property)) {
                        $this->walk($visitee, $visitor, $property, $seen);
                    }
                }

                foreach ((array) $visitee as $class) {
                    if ($candidate instanceof $class) {
                        $visitor($candidate);
                        break;
                    }
                }
            }
        }
    }
}
