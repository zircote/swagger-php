<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Contracts;

use OpenApi\Utils\AttributeFactory;

/**
 * Something that reads attributes from PHP itself, such as those of a parent class.
 *
 * The builder hands each of its augmenters, resolvers and mergers that implements this the
 * factory it assembled the document with, when the build runs. Its translators and its token
 * scanner's cache are then shared.
 */
interface AttributeFactoryAwareInterface
{
    public function setAttributeFactory(AttributeFactory $attributeFactory): static;
}
