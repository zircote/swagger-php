<?php declare(strict_types=1);

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Tests\OpenApiTestCase;
use OpenApi\Validator;

final class ValidatorTest extends OpenApiTestCase
{
    public function testValidate(): void
    {
        $validator  = new Validator($this->getTrackingLogger());

        $cases = [
            'openapi' => [$this->createOpenApiWithInfo(), true],
             'mixed-type' => [new OA\Schema(['exclusiveMinimum' => true, '_context' => $this->getContext()]), true],
             ];

        foreach ($cases as $case) {
            [$annotation, $expected] = $case;
            $this->assertEquals($expected, $validator->validate(new Analysis([$annotation], $this->getContext()), $annotation));
        }
    }
}
