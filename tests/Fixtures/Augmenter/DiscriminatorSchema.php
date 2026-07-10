<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

#[OA\Schema(
    schema: 'DiscriminatorSchema',
    oneOf: [new OA\Schema(ref: RefTarget::class)],
    discriminator: new OA\Discriminator(propertyName: 'type', mapping: ['target' => RefTarget::class]),
)]
class DiscriminatorSchema
{
}
