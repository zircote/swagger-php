<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Type;

use Symfony\Component\TypeInfo\Exception\InvalidArgumentException;
use Symfony\Component\TypeInfo\Type;
use Symfony\Component\TypeInfo\Type\ArrayShapeType;
use Symfony\Component\TypeInfo\Type\BuiltinType;
use Symfony\Component\TypeInfo\Type\CollectionType;
use Symfony\Component\TypeInfo\Type\CompositeTypeInterface;
use Symfony\Component\TypeInfo\Type\GenericType;
use Symfony\Component\TypeInfo\Type\IntersectionType;
use Symfony\Component\TypeInfo\Type\NullableType;
use Symfony\Component\TypeInfo\Type\ObjectType;
use Symfony\Component\TypeInfo\Type\TemplateType;
use Symfony\Component\TypeInfo\Type\UnionType;
use Symfony\Component\TypeInfo\TypeIdentifier;

/**
 * Replaces the templates in a type with the types they are bound to, such as `T` with `Customer`
 * in `list<T>`.
 *
 * Type-info has no helper for this, so the type is rebuilt wherever a template can occur in it.
 */
final class TemplateBinder
{
    /**
     * The type with each template replaced by the type it is bound to. A template without a
     * binding stays as it is.
     *
     * @param array<string, Type> $bindings by template name
     *
     * @throws InvalidArgumentException where the type cannot take what a template is bound to, such as `int` in `T&\Countable`
     */
    public function bind(Type $type, array $bindings): Type
    {
        return match (true) {
            $type instanceof TemplateType => $bindings[$type->getName()] ?? $type,
            $type instanceof NullableType => Type::nullable($this->bind($type->getWrappedType(), $bindings)),
            // a shape is a collection too, rebuilt as one it would lose its keys
            $type instanceof ArrayShapeType => $this->bindArrayShape($type, $bindings),
            $type instanceof CollectionType => new CollectionType(
                $type->getWrappedType() instanceof GenericType ? $this->bindGeneric($type->getWrappedType(), $bindings) : $type->getWrappedType(),
                $type->isList(),
            ),
            $type instanceof GenericType => $this->bindGeneric($type, $bindings),
            $type instanceof UnionType => $this->bindUnion($type, $bindings),
            $type instanceof IntersectionType => Type::intersection(...array_map(fn (Type $member): Type => $this->bind($member, $bindings), $type->getTypes())),
            default => $type,
        };
    }

    /**
     * Whether a template occurs anywhere in the type. Type-info's own `traverse()` does not reach
     * a generic type's arguments.
     */
    public function hasTemplate(Type $type): bool
    {
        return match (true) {
            $type instanceof TemplateType => true,
            $type instanceof NullableType => $this->hasTemplate($type->getWrappedType()),
            $type instanceof ArrayShapeType => $this->anyHasTemplate([
                ...array_column($type->getShape(), 'type'),
                ...array_filter([$type->getExtraValueType()]),
            ]),
            $type instanceof CollectionType => $this->hasTemplate($type->getWrappedType()),
            $type instanceof GenericType => $this->anyHasTemplate($type->getVariableTypes()),
            $type instanceof CompositeTypeInterface => $this->anyHasTemplate($type->getTypes()),
            default => false,
        };
    }

    /**
     * @param array<Type> $types
     */
    private function anyHasTemplate(array $types): bool
    {
        foreach ($types as $type) {
            if ($this->hasTemplate($type)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<string, Type> $bindings
     */
    private function bindArrayShape(ArrayShapeType $type, array $bindings): ArrayShapeType
    {
        $shape = array_map(
            fn (array $member): array => ['type' => $this->bind($member['type'], $bindings), 'optional' => $member['optional']],
            $type->getShape(),
        );
        $extraValueType = $type->getExtraValueType();

        return new ArrayShapeType(
            $shape,
            $type->getExtraKeyType(),
            $extraValueType instanceof Type ? $this->bind($extraValueType, $bindings) : null,
        );
    }

    /**
     * A union that takes `mixed` is `mixed`, which type-info does not allow in a union. One whose
     * members come out the same is that member.
     *
     * @param UnionType<Type>     $type
     * @param array<string, Type> $bindings
     */
    private function bindUnion(UnionType $type, array $bindings): Type
    {
        $members = array_map(fn (Type $member): Type => $this->bind($member, $bindings), $type->getTypes());
        foreach ($members as $member) {
            if ($member->isIdentifiedBy(TypeIdentifier::MIXED)) {
                return Type::mixed();
            }
        }

        $members = array_values(array_unique($members));

        return count($members) === 1 ? $members[0] : Type::union(...$members);
    }

    /**
     * @template T of BuiltinType<TypeIdentifier::ARRAY>|BuiltinType<TypeIdentifier::ITERABLE>|ObjectType<class-string>
     *
     * @param GenericType<T>      $type
     * @param array<string, Type> $bindings
     *
     * @return GenericType<T>
     */
    private function bindGeneric(GenericType $type, array $bindings): GenericType
    {
        return new GenericType($type->getWrappedType(), ...array_map(fn (Type $variable): Type => $this->bind($variable, $bindings), $type->getVariableTypes()));
    }
}
