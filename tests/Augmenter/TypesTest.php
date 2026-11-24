<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Augmenter;
use OpenApi\Builder;
use OpenApi\HybridBridge;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Tests\Concerns\AssemblesSpecification;
use OpenApi\Tests\Concerns\ExpectsLogEntries;
use OpenApi\Tests\Fixtures;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TypesTest extends TestCase
{
    use AssemblesSpecification;
    use ExpectsLogEntries;

    public function testInfersPropertyTypes(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\TypeSchema::class);

        (new Augmenter\Types())($spec);

        $schema = $spec->schemas[0];
        $this->assertSame('TypeSchema', $schema->component);

        $props = [];
        foreach ($schema->properties as $property) {
            $props[$property->property] = $property->schema;
        }

        $this->assertSame('integer', $props['id']->type);
        $this->assertSame('string', $props['name']->type);
        $this->assertSame('number', $props['score']->type);
        $this->assertTrue($props['score']->nullable);
        $this->assertSame('boolean', $props['active']->type);
        $this->assertSame('array', $props['tags']->type);
        $this->assertInstanceOf(OA\Schema::class, $props['tags']->items);
        $this->assertSame('string', $props['tags']->items->type);
    }

    public function testInfersDateTimeForASubclass(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\DateTimeSubclass::class);

        (new Augmenter\Types())($spec);

        $props = [];
        foreach ($spec->schemas[0]->properties as $property) {
            $props[$property->property] = $property->schema;
        }

        foreach (['created', 'deleted', 'updated'] as $name) {
            $this->assertSame('string', $props[$name]->type, $name);
            $this->assertSame('date-time', $props[$name]->format, $name);
        }
        $this->assertTrue($props['deleted']->nullable);
        $this->assertSame('array', $props['history']->type);
        $items = $props['history']->items;
        $this->assertInstanceOf(OA\Schema::class, $items);
        $this->assertSame('string', $items->type);
        $this->assertSame('date-time', $items->format);
    }

    public function testAStringThatRulesOutEmptyGetsAMinimumLength(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\StringPseudoTypes::class);

        (new Augmenter\Types())($spec);

        $props = [];
        foreach ($spec->schemas[0]->properties as $property) {
            $props[$property->property] = $property->schema;
        }

        $this->assertSame(1, $props['name']->minLength);
        $this->assertSame(1, $props['code']->minLength);
        $this->assertTrue($props['code']->nullable);
        $this->assertNull($props['amount']->minLength);
        $this->assertInstanceOf(OA\Schema::class, $props['tags']->items);
        $this->assertSame(1, $props['tags']->items->minLength);
        $this->assertSame([null, 1], array_map(static fn (OA\Schema $member): ?int => $member->minLength, $props['key']->oneOf ?? []));
        $this->assertSame(0, $props['note']->minLength);
    }

    public function testFillsTheItemsAnAttributeGivesWithoutAType(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\AttributeItems::class);

        (new Augmenter\Types())($spec);

        $props = [];
        foreach ($spec->schemas[0]->properties as $property) {
            $props[$property->property] = $property->schema;
        }

        $items = static fn (string $name): OA\Schema => $props[$name]->items instanceof OA\Schema ? $props[$name]->items : throw new \LogicException($name);

        $this->assertSame(['integer', 1], [$items('empty')->type, $items('empty')->minimum]);
        $this->assertSame(['integer', 'An id'], [$items('described')->type, $items('described')->description]);
        $this->assertSame('string', $items('typed')->type);
        $this->assertNull($items('referenced')->type);
        $this->assertSame('array', $items('nested')->type);
        $this->assertInstanceOf(OA\Schema::class, $items('nested')->items);
        $this->assertSame('integer', $items('nested')->items->type);
        $this->assertInstanceOf(OA\Schema::class, $props['map']->additionalProperties);
        $this->assertSame(['integer', 1], [$props['map']->additionalProperties->type, $props['map']->additionalProperties->minimum]);
        $this->assertFalse($props['closed']->additionalProperties);
        $this->assertSame(['integer', 1], [$items('shortcut')->type, $items('shortcut')->minimum]);
        $this->assertSame('A count', $props['shortcut']->description);
        $this->assertSame(['integer', false], [$items('notNull')->type, $items('notNull')->nullable]);
    }

    public function testFillsTheItemsAParameterSchemaGivesWithoutAType(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\AttributeItemsController::class);

        (new Augmenter\Types())($spec);

        $schema = $spec->operations[0]->parameters[0]->schema;
        $this->assertInstanceOf(OA\Schema::class, $schema?->items);
        $this->assertSame(['integer', 1], [$schema->items->type, $schema->items->minimum]);
    }

    /**
     * @return iterable<string, array{Builder\Mode}>
     */
    public static function modesProvider(): iterable
    {
        yield 'spec' => [Builder\Mode::SPEC];
        yield 'hybrid' => [Builder\Mode::HYBRID];
    }

    #[DataProvider('modesProvider')]
    public function testWarnsForAClassWithNoSchema(Builder\Mode $mode): void
    {
        $holder = Fixtures\Augmenter\UndescribedClasses::class;
        $money = Fixtures\Augmenter\Money::class;
        $this->expectLogEntry("Type \"{$money}\" in {$holder}::\$price matches no schema, omitted", 'error');
        $this->expectLogEntry('Type "' . Fixtures\Augmenter\Labelled::class . "\" in {$holder}::\$label matches no schema, omitted", 'error');
        $this->expectLogEntry("Type \"{$money}\" in {$holder}::\$history matches no schema, omitted", 'error');
        $this->expectLogEntry("Type \"{$money}\" in {$holder}::\$amount matches no schema, omitted with the rest of its union", 'error');
        $this->allowLogEntry('Document is missing info');

        $properties = (new Builder())
            ->setMode($mode)
            ->setLogger($this->trackingLogger())
            ->withAugmenters(static fn ($augmenters) => $augmenters->configure(['cleanup.enabled=false']))
            ->addSource([new \ReflectionClass($holder), new \ReflectionClass(Fixtures\Augmenter\RefTarget::class)])
            ->build()
            ->toArray()['components']['schemas']['UndescribedClasses']['properties'];

        // a component and an enum are referenced, and say nothing
        $this->assertSame('#/components/schemas/RefTarget', $properties['target']['$ref']);
        $this->assertSame('#/components/schemas/BackedStringEnum', $properties['status']['$ref']);
        // a union with an unmatched member matches anything, so none of its members are kept
        $this->assertEquals(new \stdClass(), $properties['amount']);
    }

    public function testAnExplicitTypeDropsThePhpTypesNull(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\ExplicitTypeNullability::class);

        (new Augmenter\Shortcuts())($spec);
        (new Augmenter\Types())($spec);

        $nullable = [];
        foreach ($spec->schemas[0]->properties as $property) {
            $nullable[$property->property] = $property->schema->nullable;
        }

        $this->assertSame([
            'explicit' => null,
            'explicitWithNull' => null,
            'explicitNullable' => true,
            'inferred' => true,
            'impliedByItems' => true,
            'itemsShortcut' => true,
        ], $nullable);
        $this->assertSame(['object', 'null'], $spec->schemas[0]->properties[1]->schema->type);
    }

    public function testInfersParameterSchema(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\TypeController::class);

        (new Augmenter\Types())($spec);

        $params = $spec->operations[0]->parameters;
        $this->assertSame('integer', $params[0]->schema->type);
        $this->assertTrue($params[0]->required);

        $this->assertSame('string', $params[1]->schema->type);
        $this->assertNull($params[1]->schema->nullable);
        $this->assertFalse($params[1]->required);
    }

    public function testAParameterWithADefaultIsNotRequired(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\ParameterDefaults::class);

        (new Augmenter\Types())($spec);

        $required = [];
        foreach ($spec->operations[0]->parameters as $parameter) {
            $required[$parameter->name] = $parameter->required;
        }

        $this->assertSame(['limit' => true, 'id' => true, 'page' => false, 'sort' => true], $required);
    }

    public function testABridgedParameterKeepsClassicsRule(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\ParameterDefaults::class);
        foreach ($spec->operations[0]->parameters as $parameter) {
            $parameter->setMeta(HybridBridge::class, true);
        }

        (new Augmenter\Types())($spec);

        $this->assertTrue($spec->operations[0]->parameters[2]->required);
    }

    public function testAQuerystringParameterGetsNoSchema(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\QuerystringParameter::class);

        (new Augmenter\Types())($spec);

        $parameter = $spec->operations[0]->parameters[0];
        $this->assertSame('search', $parameter->name);
        $this->assertNull($parameter->schema);
        $this->assertTrue($parameter->required);
    }

    public function testInfersParameterName(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\TypeController::class);

        (new Augmenter\Types())($spec);

        $this->assertSame('filter', $spec->operations[0]->parameters[1]->name);
    }

    public function testSkipsExplicitSchema(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\TypeController::class);
        $spec->operations[0]->parameters[0]->schema = new OA\Schema(type: 'string');

        (new Augmenter\Types())($spec);

        $this->assertSame('string', $spec->operations[0]->parameters[0]->schema->type);
    }

    public function testResolvesRefFromObjectType(): void
    {
        $spec = $this->assemble(
            Fixtures\Augmenter\RefTarget::class,
            Fixtures\Augmenter\RefController::class,
        );

        (new Augmenter\Types())($spec);

        $schema = $spec->operations[0]->responses[0]->content[0]->schema;
        $this->assertSame(Fixtures\Augmenter\RefTarget::class, $schema->ref);
    }

    /**
     * @return iterable<string, array{OA\Schema, string|null}>
     */
    public static function untypedSchemaProvider(): iterable
    {
        yield 'items' => [new OA\Schema(items: new OA\Schema(type: 'string')), 'array'];
        yield 'properties' => [new OA\Schema(properties: [new OA\Property(property: 'name')]), 'object'];
        yield 'patternProperties' => [new OA\Schema(patternProperties: ['^x-' => new OA\Schema(type: 'string')]), 'object'];
        // what is composed decides the type, and it may not be an object
        yield 'allOf' => [new OA\Schema(allOf: [new OA\Schema(ref: '#/components/schemas/Code'), new OA\Schema(maxLength: 3)]), null];
    }

    #[DataProvider('untypedSchemaProvider')]
    public function testInfersSchemaType(OA\Schema $schema, ?string $expected): void
    {
        $spec = new Specification();
        $spec->schemas[] = $schema;

        (new Augmenter\Types())($spec);

        $this->assertSame($expected, $schema->type);
    }
}
