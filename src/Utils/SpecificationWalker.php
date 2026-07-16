<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Utils;

use OpenApi\AttributeInterface;
use OpenApi\Spec as OA;
use OpenApi\Specification;

/**
 * Traversal helpers for walking the Specification tree.
 */
class SpecificationWalker
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
     * Walk every ref-bearing attribute in the specification.
     *
     * @param callable(OA\Schema|OA\Parameter|OA\Response|OA\Header|OA\RequestBody|OA\Link|OA\Example|OA\Security\Scheme): void $visitor
     */
    public function eachRef(callable $visitor): void
    {
        $this->visit(AttributeInterface::class, function (AttributeInterface $attribute) use ($visitor): void {
            if (
                $attribute instanceof OA\Schema
                || $attribute instanceof OA\Parameter
                || $attribute instanceof OA\Response
                || $attribute instanceof OA\Header
                || $attribute instanceof OA\RequestBody
                || $attribute instanceof OA\Link
                || $attribute instanceof OA\Example
                || $attribute instanceof OA\Security\Scheme
            ) {
                if (isset($attribute->ref)) {
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
            }

            // special case
            if ($attribute instanceof OA\Security\Requirement) {
                foreach (array_keys($attribute->toArray()) as $schemeName) {
                    $visitor(new OA\Security\Scheme(ref: '#/components/securitySchemes/' . $schemeName));
                }
            }
        });
    }

    /**
     * @template T of AttributeInterface
     *
     * @param class-string<T>   $visitee
     * @param callable(T): void $visitor
     */
    public function visit(string $visitee, callable $visitor): void
    {
        foreach (get_object_vars($this->specification) as $buckets) {
            $this->walk($visitee, $visitor, $buckets instanceof AttributeInterface ? [$buckets] : (array) $buckets);
        }
    }

    /**
     * @template T of AttributeInterface
     *
     * @param class-string<T>   $visitee
     * @param callable(T): void $visitor
     */
    protected function walk(string $visitee, callable $visitor, array $candidates, ?\SplObjectStorage $seen = null): void
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

                if ($candidate instanceof $visitee) {
                    $visitor($candidate);
                }
            }
        }
    }
}
