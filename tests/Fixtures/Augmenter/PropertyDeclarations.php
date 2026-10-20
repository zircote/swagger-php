<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

enum PetStatus: string
{
    case New = 'new';
    case Sold = 'sold';
}

enum PetSize
{
    case Small;
}

#[OA\Schema(component: 'PropertyDeclarations')]
class PropertyDeclarations
{
    #[OA\Property]
    public int $id;

    #[OA\Property]
    public ?string $nickname;

    #[OA\Property]
    public string $status = 'new';

    #[OA\Property]
    public int $legs = 4;

    #[OA\Property]
    public ?string $note = null;

    /** @var list<string> */
    #[OA\Property]
    public array $tags = ['pet'];

    #[OA\Property]
    public PetStatus $state = PetStatus::New;

    #[OA\Property]
    public PetSize $size = PetSize::Small;

    #[OA\Property]
    public \DateTimeImmutable $born;

    #[OA\Property]
    public $untyped = 'x';

    #[OA\Property]
    #[OA\Schema(type: 'string')]
    public int|string $code = 0;

    #[OA\Property]
    #[OA\Schema(default: 'kept')]
    public string $explicit = 'php';

    #[OA\Property]
    #[OA\Schema(default: null)]
    public string $suppressed = 'php';

    public function __construct(
        #[OA\Property]
        public bool $vaccinated = false,
        #[OA\Property]
        public string $owner = '',
        #[OA\Property]
        public \DateTimeImmutable $adopted = new \DateTimeImmutable('2020-01-01'),
    ) {
    }
}

#[OA\Schema(component: 'ExplicitRequired', required: ['name'])]
class ExplicitRequired
{
    #[OA\Property]
    public int $id;

    #[OA\Property]
    public ?string $name;
}
