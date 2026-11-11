<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\ComponentNamer;

use OpenApi\Type\TypeArguments;
use Radebatz\TypeInfoExtras\Type\ClassLikeType;
use Radebatz\TypeInfoExtras\Type\IntRangeType;
use Symfony\Component\TypeInfo\Type;
use Symfony\Component\TypeInfo\Type\ArrayShapeType;
use Symfony\Component\TypeInfo\Type\BuiltinType;
use Symfony\Component\TypeInfo\Type\CollectionType;
use Symfony\Component\TypeInfo\Type\GenericType;
use Symfony\Component\TypeInfo\Type\IntersectionType;
use Symfony\Component\TypeInfo\Type\NullableType;
use Symfony\Component\TypeInfo\Type\ObjectType;
use Symfony\Component\TypeInfo\Type\UnionType;
use Symfony\Component\TypeInfo\TypeIdentifier;

/**
 * Writes a generic class's type arguments as the `of` part of a component name, so the name keeps
 * their structure.
 *
 * `_` goes between arguments, `-of.` opens a nested list and `.-` closes it where more follows, and
 * `.or.` and `.and.` join a union's and an intersection's members. `-`, `.` and `_` inside a value
 * are escaped as `-h`, `-d` and `-u`, and any other character a name does not allow as `-x` and the
 * hex of each byte. A type keyword with hyphens is written in camel case, `nonEmptyString` for
 * `non-empty-string`, and `class-string<Customer>` is `classString-of.Customer`. Plain uses read as
 * before: `Envelope<Customer>` is `-of.Customer`, `Pair<A, B>` is `-of.A_B`, and
 * `Envelope<Envelope<Customer>>` is `-of.Envelope-of.Customer`. `Pair<Envelope<A>, B>` is
 * `-of.Envelope-of.A.-_B`, the close saying `B` is `Pair`'s argument, and `Envelope<Pair<A, B>>` is
 * `-of.Pair-of.A_B`.
 *
 * The closes at the end are left out. That relies on nothing following the arguments in a name,
 * which `ComponentKey` sees to by putting `of` after every other qualifier.
 */
final class TypeArgumentNames
{
    private const OPEN = '-of.';

    private const SEPARATOR = '_';

    private const CLOSE = '.-';

    private const UNION = '.or.';

    private const INTERSECTION = '.and.';

    private const SHAPE = 'shape';

    private const KEY = '.is.';

    private const OPTIONAL_KEY = '.opt.';

    private const UNSEALED = 'etc';

    private const ESCAPES = ['-' => '-h', '.' => '-d', '_' => '-u'];

    /**
     * @param \Closure(string): string $nameValue names a class, or any other value, as the namer names it in a qualifier
     */
    public function __construct(
        private \Closure $nameValue,
    ) {
    }

    /**
     * The arguments, opened like any nested list. Every list is closed, and the closes nothing
     * follows are dropped.
     */
    public function write(TypeArguments $arguments): string
    {
        $written = self::OPEN . implode(self::SEPARATOR, array_map($this->typeArgument(...), $arguments->types));
        while (str_ends_with($written, self::CLOSE)) {
            $written = substr($written, 0, -strlen(self::CLOSE));
        }

        return $written;
    }

    private function typeArgument(Type $type): string
    {
        return match (true) {
            $type instanceof NullableType => $this->typeArgument($type->getWrappedType()) . self::UNION . 'null',
            $type instanceof UnionType => implode(self::UNION, array_map($this->typeArgument(...), $type->getTypes())),
            $type instanceof IntersectionType => implode(self::INTERSECTION, array_map($this->typeArgument(...), $type->getTypes())),
            $type instanceof IntRangeType => 'int' . self::OPEN . implode(self::SEPARATOR, array_map(
                fn (int $bound): string => $this->escape(match ($bound) {
                    \PHP_INT_MIN => 'min',
                    \PHP_INT_MAX => 'max',
                    default => (string) $bound,
                }),
                [$type->getFrom(), $type->getTo()],
            )) . self::CLOSE,
            // a shape is a collection too, named as one it would lose its keys
            $type instanceof ArrayShapeType => $this->shapeArgument($type),
            $type instanceof CollectionType => $this->collectionArgument($type),
            $type instanceof GenericType => $this->typeArgumentList($this->typeArgument($type->getWrappedType()), $type->getVariableTypes()),
            $type instanceof ObjectType => $this->escape(($this->nameValue)(ltrim($type->getClassName(), '\\'))),
            $type instanceof ClassLikeType => $this->typeArgumentList($this->keyword($type->getExplicitType()), [$type->getObjectType()]),
            default => $this->escape(($this->nameValue)($this->keyword((string) $type))),
        };
    }

    /**
     * A type keyword written with hyphens, such as `non-empty-string`, in camel case:
     * `nonEmptyString`. Without hyphens, nothing in it needs escaping.
     */
    private function keyword(string $type): string
    {
        return preg_match('/^[a-z]+(?:-[a-z]+)+$/', $type) === 1
            ? lcfirst(str_replace('-', '', ucwords($type, '-')))
            : $type;
    }

    /**
     * A collection as its kind, `list`, `array` or `iterable`, with the types it is written with.
     *
     * @param CollectionType<BuiltinType<TypeIdentifier::ARRAY>|BuiltinType<TypeIdentifier::ITERABLE>|ObjectType<class-string>|GenericType<BuiltinType<TypeIdentifier::ARRAY>|BuiltinType<TypeIdentifier::ITERABLE>|ObjectType<class-string>>> $type
     */
    private function collectionArgument(CollectionType $type): string
    {
        if ($type->isList()) {
            return $this->typeArgumentList('list', [$type->getCollectionValueType()]);
        }

        $wrapped = $type->getWrappedType();

        return $wrapped instanceof GenericType
            ? $this->typeArgumentList($this->typeArgument($wrapped->getWrappedType()), $wrapped->getVariableTypes())
            : $this->typeArgument($wrapped);
    }

    /**
     * An array shape as its members, each key with its type, and `etc` where it is unsealed.
     */
    private function shapeArgument(ArrayShapeType $type): string
    {
        $members = [];
        foreach ($type->getShape() as $key => $member) {
            $members[] = $this->escape(($this->nameValue)((string) $key))
                . ($member['optional'] ? self::OPTIONAL_KEY : self::KEY)
                . $this->typeArgument($member['type']);
        }

        $extraKeyType = $type->getExtraKeyType();
        $extraValueType = $type->getExtraValueType();
        if ($extraKeyType instanceof Type && $extraValueType instanceof Type) {
            // `...` alone, as type-info writes it, takes any key and any value
            $default = $extraKeyType->isIdentifiedBy(TypeIdentifier::INT) && $extraKeyType->isIdentifiedBy(TypeIdentifier::STRING)
                && $extraValueType->isIdentifiedBy(TypeIdentifier::MIXED);
            $members[] = $this->typeArgumentList(self::UNSEALED, $default ? [] : [$extraKeyType, $extraValueType]);
        }

        return $members === []
            ? self::SHAPE
            : self::SHAPE . self::OPEN . implode(self::SEPARATOR, $members) . self::CLOSE;
    }

    /**
     * @param list<Type> $arguments
     */
    private function typeArgumentList(string $name, array $arguments): string
    {
        return $arguments === []
            ? $name
            : $name . self::OPEN . implode(self::SEPARATOR, array_map($this->typeArgument(...), $arguments)) . self::CLOSE;
    }

    /**
     * A value with the characters the tokens are made of escaped, so every `.` and `_` in the
     * arguments is a token, and so is every `-` not followed by an escape letter.
     */
    private function escape(string $value): string
    {
        // any other byte a name does not allow as `-x` and two hex digits, so it is not lost either
        return (string) preg_replace_callback(
            '/[^a-zA-Z0-9._-]/',
            static fn (array $match): string => '-x' . bin2hex($match[0]),
            strtr($value, self::ESCAPES),
        );
    }
}
