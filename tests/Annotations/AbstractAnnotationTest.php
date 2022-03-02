<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Annotations;

use OpenApi\Annotations\Get;
use OpenApi\Annotations\Parameter;
use OpenApi\Annotations\Schema;
use OpenApi\Generator;
use OpenApi\Tests\OpenApiTestCase;

class AbstractAnnotationTest extends OpenApiTestCase
{
    public function testVendorFields(): void
    {
        $annotations = $this->annotationsFromDocBlockParser('@OA\Get(x={"internal-id": 123})');
        $output = $annotations[0]->jsonSerialize();
        $prefixedProperty = 'x-internal-id';
        $this->assertSame(123, $output->$prefixedProperty);
    }

    public function testInvalidField(): void
    {
        $this->assertOpenApiLogEntryContains('Unexpected field "doesnot" for @OA\Get(), expecting');
        $this->annotationsFromDocBlockParser('@OA\Get(doesnot="exist")');
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
//        $this->assertOpenApiLogEntryStartsWith('@OA\Parameter(name=123,in="dunno")->maximum is a "string", expecting a "number" in ');
//        $this->assertOpenApiLogEntryStartsWith('@OA\Parameter(name=123,in="dunno")->type must be "string", "number", "integer", "boolean", "array", "file" when @OA\Parameter()->in != "body" in ');
        $parameter->validate();
    }

    public function nestedMatches()
    {
        $parameterMatch = (object) ['key' => Parameter::class, 'value' => ['parameters']];

        return [
            'unknown' => [self::class, null],
            'simple-match' => [Parameter::class, $parameterMatch],
            'invalid-annotation' => [Schema::class, null],
            'sub-annotation' => [SubParameter::class, $parameterMatch],
            'sub-sub-annotation' => [SubSubParameter::class, $parameterMatch],
            'sub-invalid' => [SubSchema::class, null],
        ];
    }

    /**
     * @dataProvider nestedMatches
     */
    public function testMatchNested($class, $expected): void
    {
        $this->assertEquals($expected, Get::matchNested($class));
    }

    public function testDuplicateOperationIdValidation()
    {
        $analysis = $this->analysisFromFixtures(['DuplicateOperationId.php']);
        $analysis->process((new Generator())->getProcessors());

        $this->assertOpenApiLogEntryContains('operationId must be unique. Duplicate value found: "getItem"');
        $this->assertFalse($analysis->validate());
    }
}

class SubSchema extends Schema
{
}

class SubParameter extends Parameter
{
}

class SubSubParameter extends SubParameter
{
}
