<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

use OpenApi\Utils\SourceLocation;

/**
 * Contract for all OpenAPI spec attributes.
 *
 * The assembler uses three metadata methods to determine how attributes
 * relate to each other:
 *
 * - isRoot(): root attributes are top-level elements that end up in the Specification
 *   (Schema, Operation, Info, etc.). Non-root attributes must be absorbed by a container.
 *
 * - merge(): defines same-reflector composition. When two attributes sit on the
 *   same PHP reflector (property, parameter, method), merge() declares which sibling
 *   types this attribute can be nested into. E.g., Schema merges into Property,
 *   filling its $schema slot.
 *
 * - contains(): defines hierarchical absorption. Attributes from inner reflectors
 *   flow up into enclosing-level attributes. contains() declares which types a
 *   container can absorb from the level below. E.g., Operation on a method contains
 *   RequestBody from a parameter; Schema on a class contains Property from members.
 */
interface AttributeInterface
{
    /**
     * Whether this attribute is a root-level element that goes directly into the Specification.
     */
    public function isRoot(): bool;

    /**
     * Sibling types this attribute can merge into on the same reflector.
     *
     * When multiple attributes are declared on the same PHP element, merge() determines
     * which sibling absorbs this attribute. The assembler finds the first sibling that
     * is an instance of one of the returned classes and nests this attribute into it.
     *
     * Return an empty array if this attribute never merges into siblings.
     *
     * @return list<class-string<AttributeInterface>>
     */
    public function merge(): array;

    /**
     * Types this attribute can absorb from inner reflector levels.
     *
     * During hierarchical resolution (class absorbs members, method absorbs parameters),
     * the assembler uses contains() to determine which inner-level attributes flow into
     * this container. The nestChild() mechanism finds the appropriate slot (typed property
     * or docblock-typed collection).
     *
     * Return an empty array if this attribute does not absorb children.
     *
     * @return list<class-string<AttributeInterface>>
     */
    public function contains(): array;

    public function getReflector(): ?\Reflector;

    public function setReflector(?\Reflector $reflector): static;

    public function getSourceLocation(): SourceLocation;
}
