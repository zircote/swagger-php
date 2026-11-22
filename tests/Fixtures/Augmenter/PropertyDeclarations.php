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

    /**
     * @var array<string, string>
     */
    #[OA\Property]
    public array $emptyMap = [];

    /**
     * @var list<string>
     */
    #[OA\Property]
    public array $emptyList = [];

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

#[OA\Schema(component: 'DeclaredRequired')]
class DeclaredRequired
{
    #[OA\Property]
    public int $id;

    #[OA\Property(required: true)]
    public ?string $nickname;

    #[OA\Property(required: true)]
    public string $status = 'new';

    #[OA\Property(required: false)]
    public int $legacyId;

    #[OA\Property(property: 'age', required: true)]
    public function getAge(): int
    {
        return 0;
    }

    #[OA\Property(property: 'label')]
    public function getLabel(): string
    {
        return '';
    }
}

#[OA\Schema(component: 'DeclaredAgainstList', required: ['id', 'name'])]
class DeclaredAgainstList
{
    #[OA\Property(required: true)]
    public int $id;

    #[OA\Property(required: false)]
    public ?string $name;

    #[OA\Property(required: true)]
    public ?string $nickname;
}

#[OA\Schema(component: 'SharedNames')]
class SharedNames
{
    #[OA\Property]
    public int $id;

    #[OA\Property(property: 'id', required: false)]
    public function getId(): int
    {
        return 0;
    }

    #[OA\Property(property: 'name', required: true)]
    public function getName(): string
    {
        return '';
    }

    #[OA\Property(property: 'name', required: true)]
    public function getDisplayName(): string
    {
        return '';
    }

    #[OA\Property(property: 'code', required: true)]
    public function getCode(): string
    {
        return '';
    }

    #[OA\Property(property: 'code', required: false)]
    public function getLegacyCode(): string
    {
        return '';
    }
}
