<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Type\Generics;

use Symfony\Component\TypeInfo\Type;

/**
 * The type arguments a generic class is used with, such as `Customer` in `Envelope<Customer>`.
 *
 * They are kept as types. As a string each type is written as type-info writes it, every class
 * name without a leading `\`, and `, ` between them, so one set of arguments has one spelling.
 */
readonly class TypeArguments implements \Stringable
{
    /** @var non-empty-list<Type> */
    public array $types;

    public function __construct(Type $type, Type ...$types)
    {
        $this->types = [$type, ...array_values($types)];
    }

    public function __toString(): string
    {
        // nested ones too, so `list<\App\Customer>` and `list<App\Customer>` are one spelling
        return (string) preg_replace('/(?<![\w\\\\])\\\\/', '', implode(', ', array_map(strval(...), $this->types)));
    }
}
