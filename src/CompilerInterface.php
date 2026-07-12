<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

/**
 * Compiles a Specification into a versioned OpenAPI document.
 */
interface CompilerInterface
{
    /**
     * The primary OpenAPI version this compiler targets (e.g. '3.1.0').
     */
    public function getVersion(): string;

    /**
     * Whether this compiler handles the given version string.
     */
    public function supports(string $version): bool;

    /**
     * Compile a Specification into a structured OpenAPI document array.
     *
     * @return array<string,mixed>
     */
    public function compile(Specification $specification): array;

    /**
     * Validate a Specification for completeness and correctness.
     *
     * @return list<array{level: string, message: string}>
     */
    public function validate(Specification $specification): array;
}
