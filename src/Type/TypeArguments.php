<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Type;

use OpenApi\Specification\ComponentKey;
use Radebatz\TypeInfoExtras\TypeResolver\StringTypeResolver;
use Symfony\Component\TypeInfo\Type;
use Symfony\Component\TypeInfo\Type\GenericType;

/**
 * The type arguments a generic class is used with, such as `Customer` in `Envelope<Customer>`.
 *
 * A `ComponentKey` holds them as one string under `ComponentKey::TYPE_ARGUMENTS`, written by
 * {@see __toString()}: each type as type-info writes it, every class name without a leading `\`,
 * and `, ` between them, so one set of arguments has one spelling wherever it is written.
 */
final readonly class TypeArguments implements \Stringable
{
    /** @var list<Type> */
    public array $types;

    public function __construct(Type ...$types)
    {
        $this->types = array_values($types);
    }

    public function __toString(): string
    {
        // nested ones too, so `list<\App\Customer>` and `list<App\Customer>` are one key
        return (string) preg_replace('/(?<![\w\\\\])\\\\/', '', implode(', ', array_map(strval(...), $this->types)));
    }

    /**
     * The arguments a key carries, or null for a key without them, or with arguments type-info
     * cannot read, such as a class that does not exist.
     */
    public static function of(ComponentKey $key): ?self
    {
        $arguments = $key->qualifiers[ComponentKey::TYPE_ARGUMENTS] ?? null;

        return is_string($arguments) ? self::parse($key->class, $arguments) : null;
    }

    /**
     * Arguments as written in a type string, in the one spelling, or as given where type-info
     * cannot read them.
     */
    public static function normalise(string $class, string $arguments): string
    {
        return (string) (self::parse($class, $arguments) ?? $arguments);
    }

    /**
     * The arguments in a type string, read as `TypeResolver` reads a docblock, so `int<0, 100>`
     * keeps its range. Null where type-info cannot read them.
     */
    private static function parse(string $class, string $arguments): ?self
    {
        /** @var array<string, self|null> $parsed what each generic type parsed to, by the type */
        static $parsed = [];

        $type = '\\' . ltrim($class, '\\') . '<' . $arguments . '>';
        if (!array_key_exists($type, $parsed)) {
            try {
                $generic = (new StringTypeResolver())->resolve($type);
                $parsed[$type] = $generic instanceof GenericType ? new self(...$generic->getVariableTypes()) : null;
            } catch (\Throwable) {
                $parsed[$type] = null;
            }
        }

        return $parsed[$type];
    }
}
