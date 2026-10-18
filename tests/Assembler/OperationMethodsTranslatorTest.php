<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Assembler;

use OpenApi\Assembler\OperationMethodsTranslator;
use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\OpenApiException;
use OpenApi\Spec as OA;
use OpenApi\Tests\Concerns\ExpectsLogEntries;
use OpenApi\Tests\Fixtures\OperationMethods;
use OpenApi\Utils\AttributeFactory;
use OpenApi\Utils\TypedList;
use PHPUnit\Framework\TestCase;

final class OperationMethodsTranslatorTest extends TestCase
{
    use ExpectsLogEntries;

    public function testTheConstructorNormalisesMethods(): void
    {
        $operation = new OA\Operation(methods: [OA\HttpMethod::Get, 'HEAD', 'get']);

        $this->assertSame(['get', 'head'], $operation->methods);
        $this->assertNull($operation->method);
    }

    public function testEachMethodGetsTheWholeDescription(): void
    {
        $this->allowLogEntry('operationId "updatePet" ignored');

        $path = $this->build()->toArray()['paths']['/pets/{id}'];

        foreach (['get', 'head'] as $method) {
            $this->assertSame([200, 404], array_keys($path[$method]['responses']), $method);
            $this->assertSame(['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']], $path[$method]['parameters'][0], $method);
        }
    }

    /**
     * Augmenters change children in place, so each method needs a copy of its own.
     */
    public function testEachMethodHoldsItsOwnCopy(): void
    {
        [$get, $head] = (new AttributeFactory())->fromReflector(new \ReflectionMethod(OperationMethods\PetController::class, 'show'));

        $this->assertInstanceOf(OA\Operation::class, $get);
        $this->assertInstanceOf(OA\Operation::class, $head);
        $this->assertSame(['get', 'head'], [$get->method, $head->method]);
        $this->assertNull($get->methods);
        $this->assertNull($head->methods);
        $this->assertNotSame($get->responses[0], $head->responses[0]);
        $this->assertNotSame($get->parameters[0], $head->parameters[0]);
        $this->assertSame($get->getReflector(), $head->getReflector());
    }

    public function testAnOperationIdTemplateIsExpandedPerMethod(): void
    {
        $this->allowLogEntry('operationId "updatePet" ignored');

        $paths = $this->build()->toArray()['paths'];

        $this->assertSame('getPet', $paths['/pets/{id}']['get']['operationId']);
        $this->assertSame('headPet', $paths['/pets/{id}']['head']['operationId']);
        $this->assertSame('getPets', $paths['/pets']['get']['operationId']);
    }

    public function testAnOperationIdWithoutTheMethodIsGeneratedInstead(): void
    {
        $this->expectLogEntry('operationId "updatePet" ignored on an operation with several methods (put, patch), add {method} to tell them apart', 'warning');

        $path = $this->build()->toArray()['paths']['/pets/{id}'];

        $this->assertNotSame($path['put']['operationId'], $path['patch']['operationId']);
        $this->assertNotSame('updatePet', $path['put']['operationId']);
        $this->assertSame('Updated', $path['patch']['responses'][204]['description']);
    }

    /**
     * Beside another operation the response could belong to either, so nothing is fanned out.
     */
    public function testNextToAnotherOperationNothingIsFannedOut(): void
    {
        $this->expectException(OpenApiException::class);
        $this->expectExceptionMessageMatches('/Ambiguous merge: .*Response matches multiple siblings on the same target \(.*Operation, .*Operation, .*Delete\)/');

        (new AttributeFactory())->fromReflector(new \ReflectionMethod(OperationMethods\MixedController::class, 'change'));
    }

    public function testMethodAndMethodsTogetherAreAnError(): void
    {
        $this->expectException(OpenApiException::class);
        $this->expectExceptionMessage('declares both `method` and `methods`, keep one of them');

        (new AttributeFactory())->fromReflector(new \ReflectionMethod(OperationMethods\BothController::class, 'show'));
    }

    public function testAnOperationFromAnEarlierTranslatorIsSplit(): void
    {
        $path = (new Builder())
            ->setMode(Mode::SPEC)
            ->setLogger($this->trackingLogger())
            ->withAttributeFactory(static fn (AttributeFactory $factory): AttributeFactory => $factory->withTranslators(
                static fn (TypedList $translators): TypedList => $translators->insert(new OperationMethods\RouteTranslator(), OperationMethodsTranslator::class),
            ))
            ->addSource(new \ReflectionClass(OperationMethods\RoutedController::class))
            ->build()
            ->toArray()['paths']['/pets/{id}'];

        foreach (['put', 'patch'] as $method) {
            $this->assertSame('Updated', $path[$method]['responses'][204]['description'], $method);
            $this->assertSame('id', $path[$method]['parameters'][0]['name'], $method);
        }
    }

    protected function build(): Builder\Result
    {
        return (new Builder())
            ->setMode(Mode::SPEC)
            ->setLogger($this->trackingLogger())
            ->addSource(new \ReflectionClass(OperationMethods\PetController::class))
            ->build();
    }
}
