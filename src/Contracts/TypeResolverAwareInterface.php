<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Contracts;

use OpenApi\Type\TypeResolver;

/**
 * Something that reads the PHP type of a property, parameter or method.
 *
 * The builder hands each of its augmenters, resolvers and mergers that implements this its own
 * type resolver when the build runs, so all of them read a type alike.
 */
interface TypeResolverAwareInterface
{
    public function setTypeResolver(TypeResolver $typeResolver): static;
}
