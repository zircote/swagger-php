<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'UsingVar',
    required: ['name'],
    attachables: [
        new OAT\Attachable(),
        new OAT\Attachable(),
    ]
)]
class UsingVar
{
    /**
     * @var string
     */
    #[OAT\Property]
    private $name;

    /**
     * @var \DateTimeInterface
     */
    #[OAT\Property(ref: '#/components/schemas/date')]
    private $createdAt;
}

#[OAT\Schema(
    schema: 'date',
    type: 'datetime',
)]
class UsingVarSchema
{
}
