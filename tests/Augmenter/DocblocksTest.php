<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Augmenter;
use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Tests\Concerns\AssemblesSpecification;
use OpenApi\Tests\Concerns\GeneratesTestMatrix;
use OpenApi\Tests\Fixtures;
use OpenApi\Undefined;
use OpenApi\Utils\DocBlockParser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class DocblocksTest extends TestCase
{
    use AssemblesSpecification;
    use GeneratesTestMatrix;

    public function testAugmentsOperationSummaryAndDescription(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\DocblockController::class);

        (new Augmenter\Docblocks())($spec);

        $this->assertSame('Get a thing.', $spec->operations[0]->summary);
        $this->assertSame('Returns the thing by ID.', $spec->operations[0]->description);
    }

    public function testAugmentsDeprecated(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\DocblockController::class);

        (new Augmenter\Docblocks())($spec);

        $this->assertTrue($spec->operations[0]->deprecated);
    }

    public function testAugmentsSchemaDescription(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\DocblockSchema::class);

        (new Augmenter\Docblocks())($spec);

        $this->assertSame('A documented schema.', $spec->schemas[0]->description);
        $this->assertTrue($spec->schemas[0]->deprecated);
    }

    public function testAugmentsParameterDescription(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\DocblockController::class);

        (new Augmenter\Docblocks())($spec);

        $this->assertSame('the thing identifier', $spec->operations[0]->parameters[0]->description);
    }

    /**
     * `null` is an explicit "no description", not an absent one — the attribute wins over
     * the docblock. Only `Undefined::UNDEFINED` invites inference.
     */
    public function testExplicitNullSuppressesInferredSchemaDescription(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\SuppressedSchema::class);

        (new Augmenter\Docblocks())($spec);

        $this->assertNull($spec->schemas[0]->description);
    }

    public function testExplicitNullSuppressesInferredOperationSummaryAndDescription(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\SuppressedController::class);

        (new Augmenter\Docblocks())($spec);

        $this->assertNull($spec->operations[0]->summary);
        $this->assertNull($spec->operations[0]->description);
    }

    public function testExplicitNullSuppressesInferredParameterDescription(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\SuppressedController::class);

        (new Augmenter\Docblocks())($spec);

        $this->assertNull($spec->operations[0]->parameters[0]->description);
    }

    /**
     * @return iterable<string, array{string, Mode}>
     */
    public static function buildExampleCases(): iterable
    {
        foreach (self::matrixCombinations([
            'version' => self::versions(),
            'mode' => [Mode::SPEC, Mode::HYBRID],
        ]) as $combo) {
            yield self::matrixKey([$combo['version'], $combo['mode']->value]) => [$combo['version'], $combo['mode']];
        }
    }

    #[DataProvider('buildExampleCases')]
    public function testBuildsTypedExamplesFromDocblocks(string $version, Mode $mode): void
    {
        $result = (new Builder())
            ->setMode($mode)
            ->setVersion($version)
            ->addSource([
                new \ReflectionClass(Fixtures\Augmenter\DocblockExample::class),
                new \ReflectionClass(Fixtures\Augmenter\DocblockExampleController::class),
            ])
            ->build();

        $this->assertTrue($result->isValid());
        $document = json_decode($result->toJson(), flags: JSON_THROW_ON_ERROR);
        $properties = $document->components->schemas->DocblockExample->properties;

        foreach ([
            'value' => '42',
            'enabled' => 'false',
            'extra' => '{"id":1}',
            'items' => '[1,2]',
            'emptyObject' => '{}',
            'emptyArray' => '[]',
            'nullable' => 'null',
            'code' => '"00123"',
            'flag' => '"false"',
            'numericCode' => '"123"',
            'explicit' => '9',
        ] as $property => $expected) {
            $this->assertTrue(property_exists($properties->$property, 'example'), $property);
            $this->assertSame($expected, json_encode($properties->$property->example, JSON_THROW_ON_ERROR), $property);
        }
    }

    /**
     * @return iterable<string, array{string|list<string>|null, ?string, string, bool}>
     */
    public static function exampleCases(): iterable
    {
        yield 'integer' => ['integer', '42', '42', false];
        yield 'zero' => ['integer', '0', '0', false];
        yield 'number' => ['number', '1.5', '1.5', false];
        yield 'integer as number' => ['number', '42', '42', false];
        yield 'integral decimal' => ['integer', '42.0', '42', false];
        yield 'integral exponent' => ['integer', '1e3', '1000', false];
        yield 'false' => ['boolean', 'false', 'false', false];
        yield 'true' => ['boolean', 'true', 'true', false];
        yield 'object' => ['object', '{"id":1}', '{"id":1}', false];
        yield 'empty object' => ['object', '{}', '{}', false];
        yield 'array' => ['array', '[1,2]', '[1,2]', false];
        yield 'empty array' => ['array', '[]', '[]', false];
        yield 'nullable type' => [['integer', 'null'], 'null', 'null', false];
        yield 'nullable flag' => ['integer', 'null', 'null', true];
        yield 'non-nullable' => ['integer', 'null', '"null"', false];
        yield 'string' => ['string', '00123', '"00123"', false];
        yield 'string with JSON value' => ['string', 'false', '"false"', false];
        yield 'union containing string' => [['integer', 'string'], '42', '"42"', false];
        yield 'union without string' => [['object', 'array'], '[]', '[]', false];
        yield 'unknown type' => [null, '42', '"42"', false];
        yield 'invalid JSON' => ['object', '{invalid}', '"{invalid}"', false];
        yield 'wrong JSON type' => ['object', '[]', '"[]"', false];
        yield 'fractional integer' => ['integer', '1.5', '"1.5"', false];
        yield 'integer overflow' => ['integer', '9223372036854775808', '"9223372036854775808"', false];
        yield 'nested integer overflow in object' => ['object', '{"id":9223372036854775808}', '"{\"id\":9223372036854775808}"', false];
        yield 'nested integer overflow in array' => ['array', '[9223372036854775808]', '"[9223372036854775808]"', false];
        yield 'quoted large integer' => ['object', '{"id":"9223372036854775808"}', '{"id":"9223372036854775808"}', false];
        yield 'non-finite number' => ['number', '1e400', '"1e400"', false];
        yield 'nested non-finite number' => ['array', '[1e400]', '"[1e400]"', false];
        yield 'missing example' => ['integer', null, json_encode(Undefined::UNDEFINED, JSON_THROW_ON_ERROR), false];
    }

    /**
     * @param string|list<string>|null $type
     */
    #[DataProvider('exampleCases')]
    public function testExampleMatchesSchemaType(string|array|null $type, ?string $example, string $expected, bool $nullable): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\DocblockExample::class);
        $schema = new OA\Schema(type: $type, nullable: $nullable);
        $spec->schemas[0]->properties[0]->schema = $schema;
        $parser = new class ($example) extends DocBlockParser {
            public function __construct(protected ?string $example)
            {
            }

            public function extractExample(string $docblock): ?string
            {
                return $this->example;
            }
        };

        (new Augmenter\Docblocks())->setParser($parser)($spec);

        $this->assertSame($expected, json_encode($schema->example, JSON_THROW_ON_ERROR));
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function explicitExamples(): iterable
    {
        yield 'null' => [null];
        yield 'zero' => [0];
        yield 'false' => [false];
        yield 'empty string' => [''];
        yield 'numeric string' => ['42'];
        yield 'empty array' => [[]];
    }

    #[DataProvider('explicitExamples')]
    public function testKeepsExplicitExample(mixed $example): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\DocblockExample::class);
        $schema = new OA\Schema(type: 'integer', example: $example);
        $spec->schemas[0]->properties[0]->schema = $schema;

        (new Augmenter\Docblocks())($spec);

        $this->assertSame($example, $schema->example);
    }

    public function testAugmentsPropertyParameters(): void
    {
        $limit = new OA\Parameter(name: 'limit', in: 'query', schema: new OA\Schema(type: 'integer'));
        $limit->setReflector(new \ReflectionProperty(Fixtures\Augmenter\DocblockPropertyParameters::class, 'limit'));
        $offset = new OA\Parameter(name: 'offset', in: 'query', schema: new OA\Schema(type: 'integer'));
        $offset->setReflector(new \ReflectionProperty(Fixtures\Augmenter\DocblockPropertyParameters::class, 'offset'));
        $spec = (new Specification())->add($limit, new OA\Operation\Get(parameters: [$offset]));

        (new Augmenter\Docblocks())($spec);

        $this->assertSame('The page size.', $limit->description);
        $this->assertSame(20, $limit->example);
        $this->assertTrue($limit->deprecated);
        $this->assertSame('the offset', $offset->description);
        $this->assertSame(0, $offset->example);
    }

    #[DataProvider('explicitExamples')]
    public function testKeepsExplicitPropertyParameterValues(mixed $example): void
    {
        $parameter = new OA\Parameter(
            name: 'limit',
            in: 'query',
            description: null,
            deprecated: false,
            schema: new OA\Schema(type: 'integer'),
            example: $example,
        );
        $parameter->setReflector(new \ReflectionProperty(Fixtures\Augmenter\DocblockPropertyParameters::class, 'limit'));
        $spec = (new Specification())->add($parameter);

        (new Augmenter\Docblocks())($spec);

        $this->assertNull($parameter->description);
        $this->assertFalse($parameter->deprecated);
        $this->assertSame($example, $parameter->example);
    }

    public function testAugmentsPropertyParameterWithExplicitDescription(): void
    {
        $parameter = new OA\Parameter(
            name: 'renamed',
            in: 'query',
            description: 'Explicit description',
            schema: new OA\Schema(type: 'integer'),
        );
        $parameter->setReflector(new \ReflectionProperty(Fixtures\Augmenter\DocblockPropertyParameters::class, 'limit'));
        $spec = (new Specification())->add($parameter);

        (new Augmenter\Docblocks())($spec);

        $this->assertSame('Explicit description', $parameter->description);
        $this->assertSame(20, $parameter->example);
        $this->assertTrue($parameter->deprecated);
    }

    public function testKeepsExplicitPropertyParameterExamples(): void
    {
        $examples = [new OA\Example(example: 'chosen', value: 10)];
        $parameter = new OA\Parameter(schema: new OA\Schema(type: 'integer'), examples: $examples);
        $parameter->setReflector(new \ReflectionProperty(Fixtures\Augmenter\DocblockPropertyParameters::class, 'limit'));
        $spec = (new Specification())->add($parameter);

        (new Augmenter\Docblocks())($spec);

        $this->assertSame(Undefined::UNDEFINED, $parameter->example);
        $this->assertSame($examples, $parameter->examples);
    }

    public function testAugmentsPropertyHeaders(): void
    {
        $limit = new OA\Header(schema: new OA\Schema(type: 'integer'), component: 'Limit');
        $limit->setReflector(new \ReflectionProperty(Fixtures\Augmenter\DocblockPropertyParameters::class, 'limit'));
        $offset = new OA\Header(header: 'X-Offset', schema: new OA\Schema(type: 'integer'));
        $offset->setReflector(new \ReflectionProperty(Fixtures\Augmenter\DocblockPropertyParameters::class, 'offset'));
        $shared = new OA\Header(header: 'X-Shared', schema: new OA\Schema(type: 'integer'));
        $shared->setReflector(new \ReflectionProperty(Fixtures\Augmenter\DocblockPropertyParameters::class, 'limit'));
        $spec = (new Specification())->add(
            $limit,
            new OA\Operation\Get(responses: [new OA\Response(headers: [$offset])]),
            new OA\Response(headers: [$shared], component: 'Shared'),
        );

        (new Augmenter\Docblocks())($spec);

        $this->assertSame('The page size.', $limit->description);
        $this->assertSame(20, $limit->example);
        $this->assertTrue($limit->deprecated);
        $this->assertSame('the offset', $offset->description);
        $this->assertSame(0, $offset->example);
        $this->assertSame(Undefined::UNDEFINED, $limit->schema->example);
        $this->assertSame('The page size.', $shared->description);
        $this->assertSame(20, $shared->example);
        $this->assertTrue($shared->deprecated);
    }

    #[DataProvider('explicitExamples')]
    public function testKeepsExplicitPropertyHeaderValues(mixed $example): void
    {
        $header = new OA\Header(
            description: '',
            deprecated: false,
            schema: new OA\Schema(type: 'integer'),
            example: $example,
            component: 'Limit',
        );
        $header->setReflector(new \ReflectionProperty(Fixtures\Augmenter\DocblockPropertyParameters::class, 'limit'));
        $spec = (new Specification())->add($header);

        (new Augmenter\Docblocks())($spec);

        $this->assertSame('', $header->description);
        $this->assertFalse($header->deprecated);
        $this->assertSame($example, $header->example);
    }

    public function testKeepsExplicitPropertyHeaderExamples(): void
    {
        $examples = [new OA\Example(example: 'chosen', value: 10)];
        $header = new OA\Header(schema: new OA\Schema(type: 'integer'), examples: $examples, component: 'Limit');
        $header->setReflector(new \ReflectionProperty(Fixtures\Augmenter\DocblockPropertyParameters::class, 'limit'));
        $spec = (new Specification())->add($header);

        (new Augmenter\Docblocks())($spec);

        $this->assertSame(Undefined::UNDEFINED, $header->example);
        $this->assertSame($examples, $header->examples);
    }
}
