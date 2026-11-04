<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Contracts;

/**
 * Something that names components, or works out the name a reference points at.
 *
 * The builder hands each of its augmenters, resolvers and mergers that implements this its own
 * namer when the build runs, so all of them name a component alike.
 */
interface ComponentNamerAwareInterface
{
    public function setNamer(ComponentNamerInterface $namer): static;
}
