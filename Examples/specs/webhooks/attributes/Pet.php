<?php declare(strict_types=1);

namespace OpenApi\Examples\Specs\Webhooks\Attributes;

use OpenApi\Attributes as OAT;

#[OAT\Schema(required: ['id', 'name'])]
final class Pet
{
    #[OAT\Property(format: 'int64')]
    public int $id;

    #[OAT\Property]
    public string $name;

    #[OAT\Property]
    public string $tag;
}
