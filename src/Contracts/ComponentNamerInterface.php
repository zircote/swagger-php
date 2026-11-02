<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Contracts;

/**
 * Names a component that is not named explicitly.
 *
 * A name can be asked for before it is set, to reference the component, and again when it is
 * set. Both have to agree, so the name depends on the component alone, not on when or how often it
 * is asked.
 */
interface ComponentNamerInterface
{
    /**
     * The name a component without one is filed under, or null to leave it inline.
     */
    public function name(AttributeInterface $component): ?string;
}
