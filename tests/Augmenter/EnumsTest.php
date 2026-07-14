<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Augmenter;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Tests\Concerns\AssemblesSpecification;
use OpenApi\Tests\Fixtures;
use PHPUnit\Framework\TestCase;

final class EnumsTest extends TestCase
{
    use AssemblesSpecification;

    public function testBasicEnumUsesNames(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\BasicEnum::class);

        (new Augmenter\Enums())($spec);

        $schema = $spec->schemas[0];
        $this->assertSame('BasicEnum', $schema->schema);
        $this->assertSame('string', $schema->type);
        $this->assertSame(['GREEN', 'BLUE', 'RED'], $schema->enum);
    }

    public function testBackedEnumWithoutTypeUsesNames(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\BackedStringEnum::class);

        (new Augmenter\Enums())($spec);

        $schema = $spec->schemas[0];
        $this->assertSame('BackedStringEnum', $schema->schema);
        $this->assertSame('string', $schema->type);
        $this->assertSame(['ACTIVE', 'INACTIVE'], $schema->enum);
    }

    public function testBackedEnumWithMatchingTypeUsesValues(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\BackedIntEnum::class);

        (new Augmenter\Enums())($spec);

        $schema = $spec->schemas[0];
        $this->assertSame('BackedIntEnum', $schema->schema);
        $this->assertSame('integer', $schema->type);
        $this->assertSame([1, 2, 3], $schema->enum);
    }

    public function testEnumNamesExtension(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\BackedIntEnum::class);

        (new Augmenter\Enums(enumNames: 'enumNames'))($spec);

        $schema = $spec->schemas[0];
        $this->assertSame(['enumNames' => ['GREEN', 'BLUE', 'RED']], $schema->x);
    }

    public function testEnumNamesNotSetForNameMode(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\BasicEnum::class);

        (new Augmenter\Enums(enumNames: 'enumNames'))($spec);

        $schema = $spec->schemas[0];
        $this->assertNull($schema->x);
    }

    public function testResolvesEnumInstancesInValues(): void
    {
        $spec = new Specification();
        $schema = new OA\Schema(
            schema: 'Test',
            enum: [Fixtures\Augmenter\BasicEnum::GREEN, Fixtures\Augmenter\BasicEnum::RED],
        );
        $spec->schemas[] = $schema;

        (new Augmenter\Enums())($spec);

        $this->assertSame(['GREEN', 'RED'], $schema->enum);
    }

    public function testResolvesBackedEnumInstancesInValues(): void
    {
        $spec = new Specification();
        $schema = new OA\Schema(
            schema: 'Test',
            enum: [Fixtures\Augmenter\BackedIntEnum::GREEN, Fixtures\Augmenter\BackedIntEnum::BLUE],
        );
        $spec->schemas[] = $schema;

        (new Augmenter\Enums())($spec);

        $this->assertSame([1, 2], $schema->enum);
    }

    public function testResolvesEnumClassStringInValues(): void
    {
        $spec = new Specification();
        $schema = new OA\Schema(
            schema: 'Test',
            enum: [Fixtures\Augmenter\BasicEnum::class],
        );
        $spec->schemas[] = $schema;

        (new Augmenter\Enums())($spec);

        $this->assertSame(['GREEN', 'BLUE', 'RED'], $schema->enum);
    }

    public function testPreservesExplicitSchemaName(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\BasicEnum::class);
        $spec->schemas[0]->schema = 'CustomName';

        (new Augmenter\Enums())($spec);

        $this->assertSame('CustomName', $spec->schemas[0]->schema);
    }
}
