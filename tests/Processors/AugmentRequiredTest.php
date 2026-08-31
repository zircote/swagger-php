<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Processors;

use OpenApi\Annotations as OA;
use OpenApi\Generator;
use OpenApi\Tests\OpenApiTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('Properties')]
final class AugmentRequiredTest extends OpenApiTestCase
{
    private function schema(string $fixture, bool $enabled): OA\Schema
    {
        $analysis = $this->analysisFromFixtures(
            [$fixture],
            $this->processorPipeline(),
            null,
            $enabled ? ['augmentRequired' => ['enabled' => true]] : []
        );

        return $analysis->openapi->components->schemas[0];
    }

    public function testDisabledByDefault(): void
    {
        $this->assertSame(Generator::UNDEFINED, $this->schema('TypedProperties.php', false)->required);
    }

    public function testMarksNonNullableTypedPropertiesRequired(): void
    {
        $required = $this->schema('TypedProperties.php', true)->required;

        // typed, non-nullable properties (incl. $ref properties) are required
        $this->assertContains('stringType', $required);
        $this->assertContains('intType', $required);
        $this->assertContains('arrayType', $required);
        $this->assertContains('namespaced', $required);
    }

    public function testLeavesNullablePropertiesOptional(): void
    {
        $required = $this->schema('TypedProperties.php', true)->required;

        $this->assertNotContains('nullableString', $required);
        $this->assertNotContains('staticNullableString', $required);
        $this->assertNotContains('mixedValue', $required);
    }

    public function testLeavesUntypedPropertiesOptional(): void
    {
        $required = $this->schema('TypedProperties.php', true)->required;

        // without a resolvable type there is nothing to assert as required
        $this->assertNotContains('undefined', $required);
        $this->assertNotContains('staticUndefined', $required);
    }

    public function testRespectsExplicitNullable(): void
    {
        $required = $this->schema('Customer.php', true)->required;

        // nullable: false makes the ?string property required
        $this->assertContains('iq', $required);
        // a nullable docblock type stays optional
        $this->assertNotContains('secondname', $required);
    }

    public function testDoesNotOverrideDeclaredRequired(): void
    {
        // the declared list is kept; the non-nullable createdAt is not appended
        $this->assertSame(['name'], $this->schema('UsingVar.php', true)->required);
    }

    public function testExplicitBooleanIsHonouredWithInferenceDisabled(): void
    {
        $schema = $this->schema('ExplicitRequired.php', false);

        // required: true is collected even though inference is off; required: false and the unflagged property are not
        $this->assertSame(['explicitlyRequired'], $schema->required);
    }

    public function testExplicitBooleanOverridesInference(): void
    {
        $schema = $this->schema('ExplicitRequired.php', true);

        // required: true wins over the nullable type; required: false wins over the non-nullable type
        $this->assertContains('explicitlyRequired', $schema->required);
        $this->assertNotContains('explicitlyOptional', $schema->required);
    }

    public function testBooleanRequiredIsConsumedOnTheProperty(): void
    {
        $schema = $this->schema('ExplicitRequired.php', false);

        // the boolean is consumed back to UNDEFINED, so it never serialises on the property where required has to be an array
        foreach ($schema->properties as $property) {
            $this->assertSame(Generator::UNDEFINED, $property->required);
        }
    }

    public function testInferenceIgnoresPropertiesWithoutAPhpMember(): void
    {
        $required = $this->schema('InlineProperties.php', true)->required;

        // a typed inline property is not backed by a PHP member, so inference leaves it out
        $this->assertNotContains('inlineTyped', $required);
        // an explicit boolean still applies to an inline property
        $this->assertContains('inlineFlagged', $required);
    }

    public function testInfersFromPromotedParametersAndMethods(): void
    {
        $required = $this->schema('PhpMemberProperties.php', true)->required;

        $this->assertContains('promoted', $required);
        $this->assertContains('computed', $required);
        $this->assertNotContains('promotedNullable', $required);
    }

    public function testAllNullablePropertiesLeaveRequiredUndefined(): void
    {
        $this->assertSame(Generator::UNDEFINED, $this->schema('AllNullableProperties.php', true)->required);
    }

    public function testRequiredFalseClearsAnEmptiedDeclaredList(): void
    {
        // required: false on the only declared entry must remove it, leaving no required list
        $this->assertSame(Generator::UNDEFINED, $this->schema('DeclaredRequiredEmptied.php', false)->required);
    }

    public function testBooleanRequiredIsConsumedOnAnUnnamedProperty(): void
    {
        $property = $this->schema('UnnamedBooleanRequired.php', false)->properties[0];

        // an unnamed property still has its boolean consumed back to UNDEFINED, never serialising as required: true
        $this->assertSame(Generator::UNDEFINED, $property->required);
    }

    public function testExplicitTrueAddsToADeclaredList(): void
    {
        // a declared list is kept and a property flagged required: true is appended to it
        $this->assertSame(['declared', 'flagged'], $this->schema('DeclaredRequiredMerged.php', false)->required);
    }

    public function testArrayRequiredOnAnObjectPropertyIsLeftUntouched(): void
    {
        $schema = $this->schema('ObjectPropertyWithRequired.php', false);

        // an array required is the object's own member list, not a boolean flag, so it is not consumed
        $this->assertSame(['street'], $schema->properties[0]->required);
        // and with no boolean and inference off, the parent gains no required list
        $this->assertSame(Generator::UNDEFINED, $schema->required);
    }
}
