<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Augmenter;
use OpenApi\Builder;
use OpenApi\ComponentNamer\ShortName;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Specification\ComponentKey;
use OpenApi\Tests\Concerns\ExpectsLogEntries;
use OpenApi\Tests\Fixtures;
use OpenApi\Tests\Fixtures\Augmenter\GenericBox;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerAwareInterface;

final class GenericsTest extends TestCase
{
    use ExpectsLogEntries;

    public function testArgumentsThatCannotBeReadAreReported(): void
    {
        $this->expectLogEntry('Type "OpenApi\\Tests\\Fixtures\\Type\\Template<Unknown>" has type arguments that cannot be read, using the plain schema; write each class with its namespace');

        $specification = $this->refTo(Fixtures\Type\Template::class, 'Unknown');
        $generics = new Augmenter\Generics();
        $generics->setLogger($this->trackingLogger());
        $generics($specification);

        $this->assertCount(2, $specification->schemas);
    }

    public function testARefIsReportedWhereItsPropertyIs(): void
    {
        $this->expectLogEntry('Type "' . GenericBox::class . '<Unknown>" has type arguments that cannot be read, using the plain schema in ' . GenericBox::class . '::$value in ');

        $specification = $this->refTo(GenericBox::class, 'Unknown');
        $specification->schemas[1]->properties[0]->setReflector(new \ReflectionProperty(GenericBox::class, 'value'));
        $generics = new Augmenter\Generics();
        $generics->setLogger($this->trackingLogger());
        $generics($specification);
    }

    public function testArgumentsForAClassWithoutTemplatesAreReported(): void
    {
        $this->expectLogEntry('Type "OpenApi\\Tests\\Fixtures\\Augmenter\\RefTarget<int>" has type arguments, but its class declares no @template, using the plain schema');

        $specification = $this->refTo(Fixtures\Augmenter\RefTarget::class, 'int');
        $generics = new Augmenter\Generics();
        $generics->setLogger($this->trackingLogger());
        $generics($specification);

        $this->assertCount(2, $specification->schemas);
    }

    public function testArgumentsNotMatchingTheTemplatesAreReported(): void
    {
        $this->expectLogEntry('Type "' . GenericBox::class . '<string, int>" does not match the templates "T" of its class, using the plain schema');

        $specification = $this->refTo(GenericBox::class, 'string, int');
        $generics = new Augmenter\Generics();
        $generics->setLogger($this->trackingLogger());
        $generics($specification);

        $this->assertCount(2, $specification->schemas);
    }

    /**
     * `Refs` resolves a ref whose arguments got no schema to the plain one, as `Types` does for a
     * use read from a type, rather than to nothing.
     */
    public function testARefusedRefReferencesThePlainSchema(): void
    {
        $this->expectLogEntry('Type "' . GenericBox::class . '<string, int>" does not match the templates "T" of its class, using the plain schema');

        $specification = $this->refTo(GenericBox::class, 'string, int');
        $logger = $this->trackingLogger();
        foreach ([new Augmenter\Generics(), new Augmenter\Names(), new Augmenter\Refs()] as $augmenter) {
            if ($augmenter instanceof LoggerAwareInterface) {
                $augmenter->setLogger($logger);
            }
            $augmenter($specification);
        }

        $this->assertSame('#/components/schemas/GenericBox', $specification->schemas[1]->properties[0]->schema->ref ?? null);
    }

    /**
     * Two argument classes with one short name, named apart by their schemas, give copies named
     * apart.
     */
    public function testACopyCarriesTheExplicitNamesOfItsArguments(): void
    {
        $specification = $this->refTo(GenericBox::class, Fixtures\Augmenter\RefTarget::class);
        $specification->schemas[] = (new OA\Schema(component: 'Target'))->setReflector(new \ReflectionClass(Fixtures\Augmenter\RefTarget::class));

        (new Augmenter\Generics())($specification);

        $copy = $specification->schemas[3] ?? null;
        $this->assertInstanceOf(OA\Schema::class, $copy);
        $this->assertSame(['arguments' => [Fixtures\Augmenter\RefTarget::class => 'Target']], $copy->getMeta(Augmenter\Generics::class));
        $this->assertSame('GenericBox-of.Target', (new ShortName())->name($copy));
    }

    /**
     * A copy of a schema with a name of its own builds on that name.
     */
    public function testACopyCarriesTheExplicitNameOfItsSchema(): void
    {
        $specification = $this->refTo(GenericBox::class, 'int');
        $specification->schemas[0]->component = 'Wrapper';

        (new Augmenter\Generics())($specification);

        $copy = $specification->schemas[2] ?? null;
        $this->assertInstanceOf(OA\Schema::class, $copy);
        $this->assertNull($copy->component);
        $this->assertSame('Wrapper-of.int', (new ShortName())->name($copy));
    }

    /**
     * `Types` drops a union with a member that has no schema, so nothing references a copy for
     * the other member.
     */
    public function testAUnionTypesDropsGetsNoCopy(): void
    {
        $specification = $this->holding(new OA\Property(property: 'either'));

        (new Augmenter\Generics())($specification);

        $this->assertCount(2, $specification->schemas);
    }

    /**
     * `Types` keeps a union with an enum once `ImpliedSchemas` made the enum's schema, so the dry
     * run has to see that schema too: `ImpliedSchemas` and `Shortcuts` run before `Generics`.
     */
    public function testAUnionWithAnEnumGetsACopy(): void
    {
        $specification = $this->holding(new OA\Property(property: 'boxOrEnum'));
        foreach ((new Builder())->getAugmenters() as $augmenter) {
            if ($augmenter instanceof Augmenter\Names) {
                break;
            }
            $augmenter($specification);
        }

        $keys = array_map(static fn (OA\Schema $schema): string => (string) $schema->getComponentKey(), $specification->schemas);
        $this->assertContains(GenericBox::class . '{"of":"int"}', $keys);
    }

    /**
     * An enum only the copy names gets its schema, so the copy's member can reference it.
     */
    public function testAnEnumOnlyACopyNamesGetsItsSchema(): void
    {
        $specification = $this->holding(new OA\Property(property: 'boxedEnum'));

        (new Augmenter\Generics())($specification);

        $keys = array_map(static fn (OA\Schema $schema): string => (string) $schema->getComponentKey(), $specification->schemas);
        $this->assertContains(Fixtures\Augmenter\BackedStringEnum::class, $keys);
        $this->assertContains((string) new ComponentKey(GenericBox::class, ['of' => Fixtures\Augmenter\BackedStringEnum::class]), $keys);
    }

    public function testCopiesNestingTooDeepAreReported(): void
    {
        $this->expectLogEntry('nests type arguments more than 10 deep, using the plain schema');

        $specification = $this->refTo(Fixtures\Augmenter\GenericNode::class, 'int');
        $specification->schemas[0]->properties = [
            (new OA\Property(property: 'next'))->setReflector(new \ReflectionProperty(Fixtures\Augmenter\GenericNode::class, 'next')),
        ];
        $generics = new Augmenter\Generics();
        $generics->setLogger($this->trackingLogger());
        $generics($specification);

        // the plain schema, the holder, and ten copies
        $this->assertCount(12, $specification->schemas);
    }

    /**
     * A key sorts a list, so the order of the arguments would be lost.
     */
    public function testArgumentsGivenAsAListAreReported(): void
    {
        $this->expectLogEntry('Type arguments for "' . GenericBox::class . '" are given as a list, using the plain schema; write them as one string, such as "int, string"');

        $specification = new Specification();
        $specification->schemas[] = (new OA\Schema())->setReflector(new \ReflectionClass(GenericBox::class));
        $specification->schemas[] = new OA\Schema(schema: 'Holder', properties: [
            new OA\Property(property: 'value', schema: new OA\Schema\Ref(ref: GenericBox::class, qualifiers: ['of' => ['string', 'int']])),
        ]);
        $generics = new Augmenter\Generics();
        $generics->setLogger($this->trackingLogger());
        $generics($specification);

        $this->assertCount(2, $specification->schemas);
    }

    /**
     * The copy is made from the description for the other qualifiers, which has `count` only.
     */
    public function testAUseWithOtherQualifiersCopiesTheirSchema(): void
    {
        $specification = $this->refTo(GenericBox::class, 'int', ['groups' => ['summary']]);
        $summary = (new OA\Schema(properties: [
            (new OA\Property(property: 'count'))->setReflector(new \ReflectionProperty(GenericBox::class, 'count')),
        ]))->setReflector(new \ReflectionClass(GenericBox::class));
        $specification->schemas[] = $summary->setComponentKey(new ComponentKey(GenericBox::class, ['groups' => ['summary']]));

        (new Augmenter\Generics())($specification);

        $copy = $specification->schemas[3] ?? null;
        $this->assertInstanceOf(OA\Schema::class, $copy);
        $this->assertEquals(new ComponentKey(GenericBox::class, ['groups' => ['summary'], 'of' => 'int']), $copy->getComponentKey());
        $this->assertSame(['count'], array_map(static fn (OA\Property $property): string => (string) $property->property, $copy->properties ?? []));
    }

    public function testAPropertyTypedWithArgumentsGetsACopy(): void
    {
        $specification = $this->holding(new OA\Property(property: 'box'));

        (new Augmenter\Generics())($specification);

        $this->assertCount(3, $specification->schemas);
    }

    /**
     * `Types` leaves a schema's own type in place, so the PHP type asks for nothing.
     */
    public function testAPropertyWithATypeOfItsOwnGetsNoCopy(): void
    {
        $specification = $this->holding(new OA\Property(property: 'box', schema: new OA\Schema(type: 'object')));

        (new Augmenter\Generics())($specification);

        $this->assertCount(2, $specification->schemas);
    }

    /**
     * `Types` keeps the schema's own `enum`, and types one with `items` or `properties` itself, so
     * the PHP type asks for nothing.
     *
     * @return iterable<string, array{OA\Schema}>
     */
    public static function schemasTypesLeavesAlone(): iterable
    {
        yield 'enum' => [new OA\Schema(enum: ['a', 'b'])];
        yield 'items' => [new OA\Schema(items: new OA\Schema(type: 'string'))];
        yield 'properties' => [new OA\Schema(properties: [new OA\Property(property: 'id')])];
    }

    #[DataProvider('schemasTypesLeavesAlone')]
    public function testAPropertyWhoseSchemaTypesLeavesAloneGetsNoCopy(OA\Schema $schema): void
    {
        $specification = $this->holding(new OA\Property(property: 'box', schema: $schema));

        (new Augmenter\Generics())($specification);

        $this->assertCount(2, $specification->schemas);
    }

    /**
     * `Types` types a parameter by a PHP parameter only, so a property's type asks for nothing.
     */
    public function testAParameterOnAPropertyGetsNoCopy(): void
    {
        $specification = $this->holding();
        $specification->parameters[] = (new OA\Parameter(name: 'box', in: 'query'))
            ->setReflector(new \ReflectionProperty(Fixtures\Augmenter\GenericBoxHolder::class, 'box'));

        (new Augmenter\Generics())($specification);

        $this->assertCount(2, $specification->schemas);
    }

    /**
     * `Types` types a parameter by its own reflector, so a method's return type asks for nothing.
     */
    public function testAParameterOnAMethodGetsNoCopy(): void
    {
        $specification = $this->holding();
        $specification->parameters[] = (new OA\Parameter(name: 'box', in: 'query'))
            ->setReflector(new \ReflectionMethod(Fixtures\Augmenter\GenericBoxHolder::class, 'boxed'));

        (new Augmenter\Generics())($specification);

        $this->assertCount(2, $specification->schemas);
    }

    /**
     * The copy for `RefTarget`, which has no schema here, is typed once, with its template bound, and
     * that use's `null` is not added afterwards.
     */
    public function testAnArgumentWithoutASchemaLeavesTheMemberUntyped(): void
    {
        $this->expectLogEntry('Type "OpenApi\Tests\Fixtures\Augmenter\RefTarget" in ' . GenericBox::class . '::$value matches no schema');

        $specification = $this->holding(new OA\Property(property: 'undescribed'));
        $logger = $this->trackingLogger();
        foreach ([new Augmenter\Generics(), new Augmenter\Types()] as $augmenter) {
            $augmenter->setLogger($logger);
            $augmenter($specification);
        }

        $value = $specification->schemas[2]->properties[0]->schema ?? null;
        $this->assertInstanceOf(OA\Schema::class, $value);
        $this->assertNull($value->type);
        $this->assertNull($value->nullable);
    }

    /**
     * A plain schema for the generic box, and one for its holder with the property given.
     */
    protected function holding(?OA\Property $property = null): Specification
    {
        $box = new OA\Schema(properties: array_map(
            static fn (string $name): OA\Property => (new OA\Property(property: $name))->setReflector(new \ReflectionProperty(GenericBox::class, $name)),
            ['value', 'count'],
        ));
        $holder = new OA\Schema(properties: $property instanceof OA\Property
            ? [$property->setReflector(new \ReflectionProperty(Fixtures\Augmenter\GenericBoxHolder::class, (string) $property->property))]
            : []);

        $specification = new Specification();
        $specification->schemas[] = $box->setReflector(new \ReflectionClass(GenericBox::class));
        $specification->schemas[] = $holder->setReflector(new \ReflectionClass(Fixtures\Augmenter\GenericBoxHolder::class));

        return $specification;
    }

    /**
     * A plain schema for the class, and one referencing it with type arguments, and any other
     * qualifiers given.
     *
     * @param class-string                       $class
     * @param array<string, scalar|list<scalar>> $qualifiers
     */
    protected function refTo(string $class, string $arguments, array $qualifiers = []): Specification
    {
        $specification = new Specification();
        $specification->schemas[] = (new OA\Schema())->setReflector(new \ReflectionClass($class));
        $specification->schemas[] = new OA\Schema(schema: 'Holder', properties: [
            new OA\Property(property: 'value', schema: new OA\Schema\Ref(ref: $class, qualifiers: ['of' => $arguments] + $qualifiers)),
        ]);

        return $specification;
    }
}
