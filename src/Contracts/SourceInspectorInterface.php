<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Contracts;

/**
 * Answers what a source file declares, and which members and traits are a class's own.
 *
 * A class's own members are the ones written in its body. Reflection lists a parent's members and
 * a trait's with them, and does not say which trait a member came from.
 */
interface SourceInspectorInterface
{
    /**
     * The classes, interfaces, traits and enums declared in a file.
     *
     * @return list<class-string>
     */
    public function classesIn(string $filename): array;

    /**
     * Whether the member is written in the class's body: not inherited, and not taken from a trait.
     *
     * @param \ReflectionClass<object> $class
     */
    public function declares(\ReflectionClass $class, \ReflectionMethod|\ReflectionProperty|\ReflectionClassConstant $member): bool;

    /**
     * The traits the class uses itself, without those of its parents or of other traits.
     *
     * @param \ReflectionClass<object> $class
     *
     * @return list<\ReflectionClass<object>>
     */
    public function directTraits(\ReflectionClass $class): array;
}
