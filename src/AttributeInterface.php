<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

/**
 * Most basic form of a spec attribute.
 *
 * With PHP 8.4+ property hooks this interface would become:
 *
 *     interface AttributeInterface
 *     {
 *         public(set) protected ?array $x { get; }
 *         public(set) protected ?\Reflector $reflector { get; set; }
 *
 *         public function allowedParents(): ?array;
 *     }
 *
 * At that point, remaining typehints against AbstractAttribute can be swapped for this interface.
 */
interface AttributeInterface
{
    /**
     * List of parent attribute classes this can be nested into.
     * Empty array = root-level only (not nestable).
     * Null = unrestricted (can appear anywhere).
     *
     * @return list<class-string>|null
     */
    public function allowedParents(): ?array;

    public function getReflector(): ?\Reflector;

    public function setReflector(?\Reflector $reflector): static;
}
