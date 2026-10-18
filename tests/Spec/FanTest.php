<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Spec;

use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\OpenApiException;
use OpenApi\Spec as OA;
use OpenApi\Tests\Concerns\AssemblesSpecification;
use OpenApi\Tests\Concerns\CollectsSpecClasses;
use OpenApi\Tests\Concerns\ExpectsLogEntries;
use OpenApi\Tests\Fixtures\Fan;
use OpenApi\Utils\AttributeFactory;
use PHPUnit\Framework\TestCase;

final class FanTest extends TestCase
{
    use AssemblesSpecification;
    use CollectsSpecClasses;
    use ExpectsLogEntries;

    /**
     * A constructor that does not forward `fan` leaves its attribute unable to fan out.
     */
    public function testEverySpecAttributeTakesFan(): void
    {
        $failures = [];

        foreach (self::allSpecClasses() as $class) {
            $arguments = ['fan' => OA\Fan::All];
            foreach ((new \ReflectionClass($class))->getConstructor()?->getParameters() ?? [] as $parameter) {
                if (!$parameter->isOptional()) {
                    $arguments[$parameter->getName()] = 'required';
                }
            }

            $attribute = new $class(...$arguments);
            if (!$attribute instanceof OA\AbstractAttribute || $attribute->getFan() !== OA\Fan::All) {
                $failures[] = sprintf('%s::__construct() does not forward $fan', $class);
            }
        }

        $this->assertEmpty($failures, implode("\n", $failures));
    }

    public function testASiblingFansOutToEveryTarget(): void
    {
        [$first, $second] = (new AttributeFactory())->fromReflector(new \ReflectionProperty(Fan\FannedSchema::class, 'value'));

        $this->assertInstanceOf(OA\Property::class, $first);
        $this->assertInstanceOf(OA\Property::class, $second);
        $this->assertSame('shared', $first->schema?->description);
        $this->assertSame('shared', $second->schema?->description);
        $this->assertNotSame($first->schema, $second->schema);
    }

    public function testAnAttributeOnAParameterMatchingSeveralOperationsIsAmbiguous(): void
    {
        $this->expectException(OpenApiException::class);
        $this->expectExceptionMessageMatches('/Ambiguous hierarchy: .*Parameter\\\\Path matches multiple containers \(.*Put, .*Delete\)\. .*`fan: Fan::All`/');

        (new AttributeFactory())->fromReflector(new \ReflectionMethod(Fan\UnsharedParameterController::class, 'change'));
    }

    public function testStackedOperationsShareAFannedResponseAndParameter(): void
    {
        $document = $this->build()->toArray();

        foreach (['put', 'delete'] as $method) {
            $operation = $document['paths']['/pets/{id}'][$method];
            $this->assertSame('No such pet', $operation['responses'][404]['description'], $method);
            $this->assertSame(['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']], $operation['parameters'][0], $method);
        }
    }

    public function testAHeaderFansOutToEveryResponse(): void
    {
        $responses = $this->build()->toArray()['paths']['/pets']['get']['responses'];

        $this->assertSame('Requests left', $responses[200]['headers']['X-Rate-Limit']['description']);
        $this->assertSame('Requests left', $responses[400]['headers']['X-Rate-Limit']['description']);
    }

    /**
     * Augmenters change children in place, so each target needs a copy of its own.
     */
    public function testEachTargetHoldsItsOwnCopy(): void
    {
        $operations = $this->build()->specification()->operations;
        $byId = array_column(array_map(static fn (OA\Operation $operation): array => [$operation->operationId, $operation], $operations), 1, 0);

        $this->assertNotSame($byId['updatePet']->parameters[0], $byId['deletePet']->parameters[0]);
        $this->assertNotSame($byId['updatePet']->parameters[0]->schema, $byId['deletePet']->parameters[0]->schema);
        $this->assertNotSame($byId['updatePet']->responses[0], $byId['deletePet']->responses[0]);
    }

    public function testAMemberMatchingSeveralClassSchemasIsAmbiguous(): void
    {
        $this->expectException(OpenApiException::class);
        $this->expectExceptionMessage('matches multiple containers (OpenApi\Spec\Schema "First", OpenApi\Spec\Schema "Second")');

        $this->assemble(Fan\TwoSchemas::class);
    }

    public function testAMemberFansOutToEveryClassSchema(): void
    {
        $schemas = $this->assemble(Fan\TwoSchemasFanned::class)->schemas;

        $this->assertSame(['First', 'Second'], array_map(static fn (OA\Schema $schema): ?string => $schema->component, $schemas));
        $this->assertCount(1, $schemas[0]->properties ?? []);
        $this->assertCount(1, $schemas[1]->properties ?? []);
        $this->assertNotSame($schemas[0]->properties[0], $schemas[1]->properties[0]);
    }

    /**
     * Fanning out is across attributes of one target type; a match by another type is still a
     * choice the author has to make.
     */
    public function testFanAllAcrossDifferentTargetTypesIsAmbiguous(): void
    {
        $this->expectException(OpenApiException::class);
        $this->expectExceptionMessageMatches('/Ambiguous merge: .*MediaType matches multiple siblings on the same target \(.*RequestBody, .*Response "201"\)\. Fan::All only puts it into several attributes of one type/');

        (new AttributeFactory())->fromReflector(new \ReflectionMethod(Fan\MixedTargets::class, 'create'));
    }

    public function testCopiesAreIndependentAllTheWayDown(): void
    {
        [$first, $second] = (new AttributeFactory())->fromReflector(new \ReflectionMethod(Fan\DeepController::class, 'change'));

        $this->assertInstanceOf(OA\Operation::class, $first);
        $this->assertInstanceOf(OA\Operation::class, $second);
        $this->assertNotSame($first->responses[0], $second->responses[0]);
        $this->assertNotSame($first->responses[0]->content[0], $second->responses[0]->content[0]);
        $this->assertNotSame($first->responses[0]->content[0]->schema, $second->responses[0]->content[0]->schema);
        $this->assertEquals($first->responses[0], $second->responses[0]);
    }

    public function testACopyKeepsMapKeysAndSharesTheReflector(): void
    {
        $reflector = new \ReflectionClass(Fan\SharedController::class);
        $operation = (new OA\Operation\Post(path: '/pets', callbacks: ['onAdopted' => new OA\PathItem(summary: 'Adopted')]))->setReflector($reflector);
        $operation->setMeta('mark', true);

        $copy = $operation->copy();

        $this->assertSame(['onAdopted'], array_keys($copy->callbacks ?? []));
        $this->assertNotSame($operation->callbacks['onAdopted'], $copy->callbacks['onAdopted']);
        $this->assertSame($reflector, $copy->getReflector());
        $this->assertTrue($copy->getMeta('mark'));
    }

    public function testOneTargetMatchedByTwoTypesWithOneSlotIsNestedOnce(): void
    {
        [$property] = (new AttributeFactory())->fromReflector(new \ReflectionProperty(Fan\OverlappingTargetsProperty::class, 'sameSlot'));

        $this->assertInstanceOf(OA\Property::class, $property);
        $this->assertCount(1, $property->schema->attachables ?? []);
        $this->assertInstanceOf(Fan\OverlappingTargets::class, $property->schema->attachables[0]);
    }

    /**
     * Which slot it takes would be decided by the order of the attribute's `merge()`.
     */
    public function testOneTargetMatchedByTwoTypesWithDifferentSlotsIsAmbiguous(): void
    {
        $this->expectException(OpenApiException::class);
        $this->expectExceptionMessage('as more than one target type (OpenApi\\Spec\\Schema, OpenApi\\Spec\\Schema\\Items), and they name different slots (attachables[], allOf[])');

        (new AttributeFactory())->fromReflector(new \ReflectionProperty(Fan\OverlappingTargetsProperty::class, 'differentSlots'));
    }

    protected function build(): Builder\Result
    {
        return (new Builder())
            ->setMode(Mode::SPEC)
            ->setLogger($this->trackingLogger())
            ->addSource(new \ReflectionClass(Fan\SharedController::class))
            ->build();
    }
}
