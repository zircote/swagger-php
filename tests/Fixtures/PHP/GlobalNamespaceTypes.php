<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

use OpenApi\Attributes as OAT;

// Global namespace on purpose: a short-name docblock resolves against the declaring class's
// namespace, and only here can that produce a leading backslash.

#[OAT\Schema(schema: 'GlobalNamespaceTarget')]
class GlobalNamespaceTarget
{
    #[OAT\Property]
    public string $name;
}

#[OAT\Schema(schema: 'GlobalNamespaceHolder')]
class GlobalNamespaceHolder
{
    /** No docblock at all — the reflection type is what gets resolved. */
    #[OAT\Property]
    public GlobalNamespaceTarget $native;

    /** @var GlobalNamespaceTarget */
    #[OAT\Property]
    public GlobalNamespaceTarget $shortName;

    /** @var \GlobalNamespaceTarget */
    #[OAT\Property]
    public GlobalNamespaceTarget $fullyQualified;

    /** @var GlobalNamespaceTarget<string> */
    #[OAT\Property]
    public GlobalNamespaceTarget $shortNameGeneric;
}
