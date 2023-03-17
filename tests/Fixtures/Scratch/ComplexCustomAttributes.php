<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Info(version: '1.0.0', title: 'API')]
class CustomAttributes
{
}

#[\Attribute(
    \Attribute::TARGET_CLASS |
    \Attribute::TARGET_METHOD |
    \Attribute::TARGET_PROPERTY |
    \Attribute::IS_REPEATABLE
)]
class Schema extends OAT\Schema
{
    /**
     * @param class-string $of
     * @param string|null  $description
     * @param array        $optional
     * @param int|null     $minLength
     * @param int|null     $maxLength
     *
     * @throws \ReflectionException
     */
    public function __construct(
        string $of,
        ?string $description = null,
        array $optional = [],
        ?int $minLength = null,
        ?int $maxLength = null,
    ) {
        $class = new \ReflectionClass($of);

        $required = null;
        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if (\in_array($property->getName(), $optional, true)) {
                continue;
            }

            $required[] = $property->getName();
        }

        $shortName = $class->getShortName();

        parent::__construct(
            schema: $shortName,
            title: $shortName,
            description: $description,
            required: $required,
            maxLength: $maxLength,
            minLength: $minLength
        );
    }
}

#[\Attribute(
    \Attribute::TARGET_METHOD |
    \Attribute::TARGET_PROPERTY |
    \Attribute::TARGET_PARAMETER |
    \Attribute::TARGET_CLASS_CONSTANT |
    \Attribute::IS_REPEATABLE
)]
class Collection extends OAT\Property
{
    /** @param class-string $of */
    public function __construct(
        string $of,
        ?string $description = null
    ) {
        $shortName = (new \ReflectionClass($of))->getShortName();

        parent::__construct(
            title: $shortName,
            description: $description,
            items: new OAT\Items(ref: "#/components/schemas/$shortName")
        );
    }
}

#[\Attribute(
    \Attribute::TARGET_METHOD |
    \Attribute::TARGET_PROPERTY |
    \Attribute::TARGET_PARAMETER |
    \Attribute::TARGET_CLASS_CONSTANT |
    \Attribute::IS_REPEATABLE
)]
class Item extends OAT\Property
{
    /** @param class-string $of */
    public function __construct(
        string $of,
        ?string $description = null
    ) {
        $shortName = (new \ReflectionClass($of))->getShortName();

        parent::__construct(
            ref: "#/components/schemas/$shortName",
            title: $shortName,
            description: $description,
        );
    }
}

#[\Attribute(
    \Attribute::TARGET_METHOD |
    \Attribute::TARGET_PROPERTY |
    \Attribute::TARGET_PARAMETER |
    \Attribute::TARGET_CLASS_CONSTANT |
    \Attribute::IS_REPEATABLE
)]
class Raw extends OAT\Property
{
    public function __construct(
        ?string $title = null,
        ?string $description = null
    ) {
        parent::__construct(
            title: $title,
            description: $description,
        );
    }
}

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Successful extends OAT\Response
{
    /** @param ?class-string $of */
    public function __construct(
        ?string $of = null,
    ) {
        if ($of === null) {
            parent::__construct(
                response: 200,
                description: 'Operation complete'
            );

            return;
        }

        $shortName = (new \ReflectionClass($of))->getShortName();

        parent::__construct(
            response: 200,
            description: "Successful response of [$shortName]",
            content: new OAT\JsonContent(
                ref: "#/components/schemas/$shortName"
            )
        );
    }
}

final class TargetGroupController
{
    #[
        OAT\Get(path: '/target_groups', operationId: 'groups', summary: 'List target groups', tags: ['Target groups']),
        Successful(of: TargetGroupListDto::class)
    ]
    public function list(): string
    {
    }
}

#[Schema(of: TargetGroupListDto::class)]
final class TargetGroupListDto
{
    public function __construct(
        /** @var TargetGroupDto[] */
        #[Collection(of: TargetGroupDto::class)]
        public readonly array $targetGroups = []
    ) {
    }
}

#[Schema(of: TargetGroupDto::class)]
final class TargetGroupDto
{
    public function __construct(
        #[OAT\Property] # with my custom attribute #[Item] I also had problems
        public readonly string $groupId,
        #[OAT\Property] # with my custom attribute #[Item] I also had problems
        public readonly string $groupName,

        /** @var TargetDto[] */
        #[Collection(of: TargetDto::class)]
        /* Same ...
        #[OAT\Property(
            title: 'TargetDto',
            items: new OAT\Items(ref: '#/components/schemas/TargetDto')
        )]
        */
        public readonly array $targets = []
    ) {
    }
}

#[Schema(of: TargetDto::class)]
final class TargetDto
{
    public function __construct(
        #[Item(of: TargetId::class)]
        public readonly string $targetId,
        #[Item(of: TargetType::class)]
        public readonly string $targetType,
        // ...
    ) {
    }
}

#[Schema(of: TargetId::class)]
class TargetId
{
}

#[Schema(of: TargetType::class)]
class TargetType
{
}
