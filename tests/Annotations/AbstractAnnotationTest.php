<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Annotations;

use OpenApi\Annotations as OA;
use OpenApi\Tests\OpenApiTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class AbstractAnnotationTest extends OpenApiTestCase
{
    public function testVendorFields(): void
    {
        $annotations = $this->annotationsFromDocBlockParser('@OA\Get(x={"internal-id": 123})');
        $output = $annotations[0]->jsonSerialize();
        $prefixedProperty = 'x-internal-id';
        $this->assertSame(123, $output->$prefixedProperty);
    }

    public function testUmergedAnnotation(): void
    {
        $openapi = $this->createOpenApiWithInfo();
        $openapi->merge($this->annotationsFromDocBlockParser('@OA\Items()'));
        $this->assertOpenApiLogEntryContains('Unexpected @OA\Items(), expected to be inside @OA\\');
        $openapi->validate();
    }

    public function testConflictedNesting(): void
    {
        $comment = <<<END
@OA\Info(
    title="Info only has one contact field..",
    version="test",
    @OA\Contact(name="first"),
    @OA\Contact(name="second")
)
END;
        $annotations = $this->annotationsFromDocBlockParser($comment);
        $this->assertOpenApiLogEntryContains('Only one @OA\Contact() allowed for @OA\Info() multiple found in:');
        $annotations[0]->validate();
    }

    public function testKey(): void
    {
        $comment = <<<END
@OA\Response(
    @OA\Header(header="X-CSRF-Token",description="Token to prevent Cross Site Request Forgery")
)
END;
        $annotations = $this->annotationsFromDocBlockParser($comment);
        $this->assertEquals('{"headers":{"X-CSRF-Token":{"description":"Token to prevent Cross Site Request Forgery"}}}', json_encode($annotations[0]));
    }

    public function testConflictingKey(): void
    {
        $comment = <<<END
@OA\Response(
    description="The headers in response must have unique header values",
    @OA\Header(header="X-CSRF-Token", @OA\Schema(type="string"), description="first"),
    @OA\Header(header="X-CSRF-Token", @OA\Schema(type="string"), description="second")
)
END;
        $annotations = $this->annotationsFromDocBlockParser($comment);
        $this->assertOpenApiLogEntryContains('Multiple @OA\Header() with the same header="X-CSRF-Token":');
        $annotations[0]->validate();
    }

    public function testRequiredFields(): void
    {
        $annotations = $this->annotationsFromDocBlockParser('@OA\Info()');
        $info = $annotations[0];
        $this->assertOpenApiLogEntryContains('Missing required field "title" for @OA\Info() in ');
        $this->assertOpenApiLogEntryContains('Missing required field "version" for @OA\Info() in ');
        $info->validate();
    }

    public function testTypeValidation(): void
    {
        $comment = <<<END
@OA\Parameter(
    name=123,
    in="dunno",
    required="maybe",
    @OA\Schema(
      type="strig",
    )
)
END;
        $annotations = $this->annotationsFromDocBlockParser($comment);
        $parameter = $annotations[0];
        $this->assertOpenApiLogEntryContains('@OA\Parameter(name=123,in="dunno")->name is a "integer", expecting a "string" in ');
        $this->assertOpenApiLogEntryContains('@OA\Parameter(name=123,in="dunno")->in "dunno" is invalid, expecting "query", "header", "path", "cookie" in ');
        $this->assertOpenApiLogEntryContains('@OA\Parameter(name=123,in="dunno")->required is a "string", expecting a "boolean" in ');
        $parameter->validate();
    }

    public static function nestedMatches(): iterable
    {
        $parameterMatch = (object) ['key' => OA\Parameter::class, 'value' => ['parameters']];

        return [
            'simple-match' => [OA\Parameter::class, $parameterMatch],
            'invalid-annotation' => [OA\Schema::class, null],
            'sub-annotation' => [SubParameter::class, $parameterMatch],
            'sub-sub-annotation' => [SubSubParameter::class, $parameterMatch],
            'sub-invalid' => [SubSchema::class, null],
        ];
    }

    #[DataProvider('nestedMatches')]
    public function testMatchNested(string $class, $expected): void
    {
        $this->assertEquals($expected, (new OA\Get([]))->matchNested(new $class([])));
    }

    public function testDuplicateOperationIdValidation(): void
    {
        $analysis = $this->analysisFromFixtures([
                'DuplicateOperationId.php',
            ], $this->processorPipeline());

        $this->assertOpenApiLogEntryContains('operationId must be unique. Duplicate value found: "getItem"');
        $this->assertFalse($analysis->validate());
    }

    public function testIsRoot(): void
    {
        $this->assertTrue((new OA\AdditionalProperties([]))->isRoot(OA\AdditionalProperties::class));
        $this->assertFalse((new OA\AdditionalProperties([]))->isRoot(OA\Schema::class));
        $this->assertTrue((new SubSchema([]))->isRoot(OA\Schema::class));
    }

    public function testValidateExamples(): void
    {
        $analysis = $this->analysisFromFixtures([
            'BadExampleParameter.php',
        ], $this->processorPipeline());

        $this->assertOpenApiLogEntryContains('Required @OA\PathItem() not found');
        $this->assertOpenApiLogEntryContains('Required @OA\Info() not found');
        $this->assertOpenApiLogEntryContains('"example" and "examples" are mutually exclusive');

        $analysis->validate();
    }
}

class SubSchema extends OA\Schema
{
}

class SubParameter extends OA\Parameter
{
}

class SubSubParameter extends SubParameter
{
}
