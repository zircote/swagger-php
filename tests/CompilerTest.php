<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Compiler\OpenApi30Compiler;
use OpenApi\Compiler\OpenApi31Compiler;
use OpenApi\Compiler\OpenApi32Compiler;
use OpenApi\CompilerInterface;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CompilerTest extends TestCase
{
    protected function createSpecification(string $version = '3.1.0'): Specification
    {
        $spec = new Specification();
        $spec->openapi = new OA\OpenApi(version: $version);
        $spec->info = new OA\Info(title: 'Test API', version: '1.0.0');

        return $spec;
    }

    protected function compileSchema(CompilerInterface $compiler, OA\Schema $schema): array
    {
        $spec = $this->createSpecification($compiler->getVersion());
        $spec->schemas[] = $schema;

        $output = $compiler->compile($spec);

        return $output['components']['schemas'][$schema->schema];
    }

    // --- Version support ---

    public function testSupportsVersions(): void
    {
        $c30 = new OpenApi30Compiler();
        $c31 = new OpenApi31Compiler();
        $c32 = new OpenApi32Compiler();

        $this->assertTrue($c30->supports('3.0.0'));
        $this->assertTrue($c30->supports('3.0.3'));
        $this->assertTrue($c30->supports('3.0.4'));
        $this->assertFalse($c30->supports('3.1.0'));

        $this->assertTrue($c31->supports('3.1.0'));
        $this->assertTrue($c31->supports('3.1.1'));
        $this->assertFalse($c31->supports('3.0.0'));
        $this->assertFalse($c31->supports('3.2.0'));

        $this->assertTrue($c32->supports('3.2.0'));
        $this->assertFalse($c32->supports('3.1.0'));
    }

    // --- Nullable handling ---

    public static function nullableProvider(): iterable
    {
        yield '3.0 type array with null → string type + nullable keyword' => [
            new OpenApi30Compiler(),
            new OA\Schema(schema: 'N', type: ['string', 'null']),
            ['type' => 'string', 'nullable' => true],
        ];

        yield '3.0 explicit nullable flag' => [
            new OpenApi30Compiler(),
            new OA\Schema(schema: 'N', type: 'integer', nullable: true),
            ['type' => 'integer', 'nullable' => true],
        ];

        yield '3.0 non-nullable stays clean' => [
            new OpenApi30Compiler(),
            new OA\Schema(schema: 'N', type: 'string'),
            ['type' => 'string'],
        ];

        yield '3.1 nullable flag → type array' => [
            new OpenApi31Compiler(),
            new OA\Schema(schema: 'N', type: 'string', nullable: true),
            ['type' => ['string', 'null']],
        ];

        yield '3.1 type array passthrough' => [
            new OpenApi31Compiler(),
            new OA\Schema(schema: 'N', type: ['string', 'null']),
            ['type' => ['string', 'null']],
        ];

        yield '3.1 non-nullable stays clean' => [
            new OpenApi31Compiler(),
            new OA\Schema(schema: 'N', type: 'string'),
            ['type' => 'string'],
        ];
    }

    #[DataProvider('nullableProvider')]
    public function testNullableHandling(CompilerInterface $compiler, OA\Schema $schema, array $expected): void
    {
        $result = $this->compileSchema($compiler, $schema);

        foreach ($expected as $key => $value) {
            $this->assertArrayHasKey($key, $result);
            $this->assertEquals($value, $result[$key]);
        }

        if (!isset($expected['nullable'])) {
            $this->assertArrayNotHasKey('nullable', $result);
        }
    }

    // --- ExclusiveMinimum/Maximum ---

    public static function exclusiveBoundsProvider(): iterable
    {
        yield '3.0 numeric exclusiveMinimum → minimum + boolean' => [
            new OpenApi30Compiler(),
            new OA\Schema(schema: 'B', type: 'integer', exclusiveMinimum: 5),
            ['minimum' => 5, 'exclusiveMinimum' => true],
        ];

        yield '3.0 numeric exclusiveMaximum → maximum + boolean' => [
            new OpenApi30Compiler(),
            new OA\Schema(schema: 'B', type: 'integer', exclusiveMaximum: 100),
            ['maximum' => 100, 'exclusiveMaximum' => true],
        ];

        yield '3.0 boolean exclusiveMinimum passthrough' => [
            new OpenApi30Compiler(),
            new OA\Schema(schema: 'B', type: 'integer', minimum: 0, exclusiveMinimum: true),
            ['minimum' => 0, 'exclusiveMinimum' => true],
        ];

        yield '3.0 minimum without exclusive stays plain' => [
            new OpenApi30Compiler(),
            new OA\Schema(schema: 'B', type: 'integer', minimum: 0),
            ['minimum' => 0],
            ['exclusiveMinimum'],
        ];

        yield '3.1 numeric exclusiveMinimum stays numeric' => [
            new OpenApi31Compiler(),
            new OA\Schema(schema: 'B', type: 'integer', exclusiveMinimum: 5),
            ['exclusiveMinimum' => 5],
            ['minimum'],
        ];

        yield '3.1 numeric exclusiveMaximum stays numeric' => [
            new OpenApi31Compiler(),
            new OA\Schema(schema: 'B', type: 'integer', exclusiveMaximum: 100),
            ['exclusiveMaximum' => 100],
            ['maximum'],
        ];
    }

    /**
     * @param list<string> $absent
     */
    #[DataProvider('exclusiveBoundsProvider')]
    public function testExclusiveBounds(CompilerInterface $compiler, OA\Schema $schema, array $expected, array $absent = []): void
    {
        $result = $this->compileSchema($compiler, $schema);

        foreach ($expected as $key => $value) {
            $this->assertArrayHasKey($key, $result, "Expected key '{$key}' in output");
            $this->assertEquals($value, $result[$key]);
        }

        foreach ($absent as $key) {
            $this->assertArrayNotHasKey($key, $result, "Key '{$key}' should not be in output");
        }
    }

    // --- $ref siblings ---

    public function test30RefStripsDescription(): void
    {
        $result = $this->compileSchema(
            new OpenApi30Compiler(),
            new OA\Schema(schema: 'Alias', description: 'Stripped', ref: '#/components/schemas/Original'),
        );

        $this->assertEquals('#/components/schemas/Original', $result['$ref']);
        $this->assertArrayNotHasKey('description', $result);
    }

    public function test31RefAllowsSiblings(): void
    {
        $result = $this->compileSchema(
            new OpenApi31Compiler(),
            new OA\Schema(schema: 'Alias', description: 'Kept', ref: '#/components/schemas/Original'),
        );

        $this->assertEquals('#/components/schemas/Original', $result['$ref']);
        $this->assertEquals('Kept', $result['description']);
    }

    // --- Schema features: 3.0 omits, 3.1 includes ---

    public static function schemaFeatureProvider(): iterable
    {
        yield 'const' => [
            new OA\Schema(schema: 'F', type: 'string', const: 'only'),
            'const',
            'only',
        ];

        yield 'examples array' => [
            new OA\Schema(schema: 'F', type: 'string', examples: ['foo', 'bar']),
            'examples',
            ['foo', 'bar'],
        ];

        yield 'unevaluatedProperties false' => [
            new OA\Schema(schema: 'F', type: 'object', unevaluatedProperties: false),
            'unevaluatedProperties',
            false,
        ];

        yield 'unevaluatedProperties schema' => [
            new OA\Schema(schema: 'F', type: 'object', unevaluatedProperties: new OA\Schema(type: 'string')),
            'unevaluatedProperties',
            ['type' => 'string'],
        ];

        yield 'prefixItems' => [
            new OA\Schema(schema: 'F', type: 'array', items: new OA\Schema(type: 'integer'), prefixItems: [new OA\Schema(type: 'string')]),
            'prefixItems',
            [['type' => 'string']],
        ];
    }

    #[DataProvider('schemaFeatureProvider')]
    public function test30OmitsSchemaFeature(OA\Schema $schema, string $key, mixed $expectedIn31): void
    {
        $result = $this->compileSchema(new OpenApi30Compiler(), $schema);

        $this->assertArrayNotHasKey($key, $result);
    }

    #[DataProvider('schemaFeatureProvider')]
    public function test31IncludesSchemaFeature(OA\Schema $schema, string $key, mixed $expectedIn31): void
    {
        $result = $this->compileSchema(new OpenApi31Compiler(), $schema);

        $this->assertArrayHasKey($key, $result);
        $this->assertEquals($expectedIn31, $result[$key]);
    }

    public function test30OmitsIfThenElse(): void
    {
        $result = $this->compileSchema(
            new OpenApi30Compiler(),
            new OA\Schema(
                schema: 'Cond',
                if: new OA\Schema(properties: [new OA\Property(property: 'type')]),
                then: new OA\Schema(required: ['value']),
                else: new OA\Schema(required: ['other']),
            ),
        );

        $this->assertArrayNotHasKey('if', $result);
        $this->assertArrayNotHasKey('then', $result);
        $this->assertArrayNotHasKey('else', $result);
    }

    public function test31IncludesIfThenElse(): void
    {
        $result = $this->compileSchema(
            new OpenApi31Compiler(),
            new OA\Schema(
                schema: 'Cond',
                if: new OA\Schema(properties: [new OA\Property(property: 'type')]),
                then: new OA\Schema(required: ['value']),
            ),
        );

        $this->assertArrayHasKey('if', $result);
        $this->assertArrayHasKey('then', $result);
    }

    // --- Webhooks ---

    public function test30OmitsWebhooks(): void
    {
        $spec = $this->createSpecification('3.0.0');
        $spec->operations[] = new OA\Operation(webhook: 'onEvent', method: 'post', operationId: 'onEvent');

        $output = (new OpenApi30Compiler())->compile($spec);

        $this->assertArrayNotHasKey('webhooks', $output);
    }

    public function test31IncludesWebhooks(): void
    {
        $spec = $this->createSpecification('3.1.0');
        $spec->operations[] = new OA\Operation(webhook: 'onEvent', method: 'post', operationId: 'onEvent');

        $output = (new OpenApi31Compiler())->compile($spec);

        $this->assertArrayHasKey('webhooks', $output);
        $this->assertArrayHasKey('onEvent', $output['webhooks']);
    }

    // --- License ---

    public function test30LicenseStripsIdentifier(): void
    {
        $spec = $this->createSpecification('3.0.0');
        $spec->info = new OA\Info(
            title: 'Test',
            version: '1.0',
            license: new OA\License(name: 'MIT', identifier: 'MIT', url: 'https://mit.edu'),
        );

        $output = (new OpenApi30Compiler())->compile($spec);

        $this->assertEquals('MIT', $output['info']['license']['name']);
        $this->assertEquals('https://mit.edu', $output['info']['license']['url']);
        $this->assertArrayNotHasKey('identifier', $output['info']['license']);
    }

    public function test31LicenseIncludesIdentifier(): void
    {
        $spec = $this->createSpecification('3.1.0');
        $spec->info = new OA\Info(
            title: 'Test',
            version: '1.0',
            license: new OA\License(name: 'MIT', identifier: 'MIT'),
        );

        $output = (new OpenApi31Compiler())->compile($spec);

        $this->assertEquals('MIT', $output['info']['license']['identifier']);
    }

    // --- Default and example values ---

    public static function defaultAndExampleProvider(): iterable
    {
        yield 'string default emitted' => [
            new OA\Schema(schema: 'D', type: 'string', default: 'hello'),
            'default', 'hello',
        ];

        yield 'null default emitted' => [
            new OA\Schema(schema: 'D', type: ['string', 'null'], default: null),
            'default', null,
        ];

        yield 'false default emitted' => [
            new OA\Schema(schema: 'D', type: 'boolean', default: false),
            'default', false,
        ];

        yield 'example emitted' => [
            new OA\Schema(schema: 'D', type: 'string', example: 'sample'),
            'example', 'sample',
        ];
    }

    #[DataProvider('defaultAndExampleProvider')]
    public function testDefaultAndExampleEmitted(OA\Schema $schema, string $key, mixed $expected): void
    {
        $result = $this->compileSchema(new OpenApi31Compiler(), $schema);

        $this->assertArrayHasKey($key, $result);
        $this->assertSame($expected, $result[$key]);
    }

    public function testUndefinedDefaultNotEmitted(): void
    {
        $result = $this->compileSchema(new OpenApi31Compiler(), new OA\Schema(schema: 'X', type: 'string'));

        $this->assertArrayNotHasKey('default', $result);
        $this->assertArrayNotHasKey('example', $result);
        $this->assertArrayNotHasKey('const', $result);
    }

    // --- Version output ---

    public function test32SetsVersionInOutput(): void
    {
        $spec = $this->createSpecification('3.2.0');

        $output = (new OpenApi32Compiler())->compile($spec);

        $this->assertEquals('3.2.0', $output['openapi']);
    }

    // --- Operations and paths ---

    public function testOperationsGroupedByPath(): void
    {
        $spec = $this->createSpecification('3.1.0');
        $spec->operations[] = new OA\Operation(path: '/users', method: 'get', operationId: 'listUsers');
        $spec->operations[] = new OA\Operation(path: '/users', method: 'post', operationId: 'createUser');
        $spec->operations[] = new OA\Operation(path: '/users/{id}', method: 'get', operationId: 'getUser');

        $output = (new OpenApi31Compiler())->compile($spec);

        $this->assertCount(2, $output['paths']);
        $this->assertArrayHasKey('get', $output['paths']['/users']);
        $this->assertArrayHasKey('post', $output['paths']['/users']);
        $this->assertArrayHasKey('get', $output['paths']['/users/{id}']);
    }

    // --- Components ---

    public function testSecuritySchemeCompiled(): void
    {
        $spec = $this->createSpecification('3.1.0');
        $spec->securitySchemes[] = new OA\Security\Scheme\Http(
            securityScheme: 'bearer',
            scheme: 'bearer',
            bearerFormat: 'JWT',
        );

        $output = (new OpenApi31Compiler())->compile($spec);
        $scheme = $output['components']['securitySchemes']['bearer'];

        $this->assertEquals('http', $scheme['type']);
        $this->assertEquals('bearer', $scheme['scheme']);
        $this->assertEquals('JWT', $scheme['bearerFormat']);
    }

    // --- Validation ---

    public static function validationProvider(): iterable
    {
        $specWithWebhook = new Specification();
        $specWithWebhook->openapi = new OA\OpenApi(version: '3.0.0');
        $specWithWebhook->info = new OA\Info(title: 'T', version: '1.0');
        $specWithWebhook->operations[] = new OA\Operation(webhook: 'ev', method: 'post');

        yield '3.0 webhooks unsupported' => [
            new OpenApi30Compiler(),
            $specWithWebhook,
            'webhooks are not supported in OpenAPI 3.0 and will be omitted',
        ];

        $specWithIdentifier = new Specification();
        $specWithIdentifier->openapi = new OA\OpenApi(version: '3.0.0');
        $specWithIdentifier->info = new OA\Info(title: 'T', version: '1.0', license: new OA\License(name: 'MIT', identifier: 'MIT'));

        yield '3.0 license identifier' => [
            new OpenApi30Compiler(),
            $specWithIdentifier,
            'License identifier is not supported in OpenAPI 3.0, use url instead',
        ];

        $specNoInfo = new Specification();
        $specNoInfo->openapi = new OA\OpenApi(version: '3.1.0');

        yield '3.1 missing info' => [
            new OpenApi31Compiler(),
            $specNoInfo,
            'info is required',
        ];

        $specArrayNoItems = new Specification();
        $specArrayNoItems->openapi = new OA\OpenApi(version: '3.0.0');
        $specArrayNoItems->info = new OA\Info(title: 'T', version: '1.0');
        $specArrayNoItems->schemas[] = new OA\Schema(schema: 'Bad', type: 'array');

        yield '3.0 array without items' => [
            new OpenApi30Compiler(),
            $specArrayNoItems,
            'Schema "Bad" has type "array" but no items',
        ];
    }

    #[DataProvider('validationProvider')]
    public function testValidation(CompilerInterface $compiler, Specification $specification, string $expectedMessage): void
    {
        $diagnostics = $compiler->validate($specification);
        $messages = array_column($diagnostics, 'message');

        $this->assertContains($expectedMessage, $messages);
    }

    // --- x- extensions ---

    public function testExtensionsEmitted(): void
    {
        $result = $this->compileSchema(
            new OpenApi31Compiler(),
            new OA\Schema(schema: 'Ext', type: 'object', x: ['custom' => 'value', 'flag' => true]),
        );

        $this->assertEquals('value', $result['x-custom']);
        $this->assertTrue($result['x-flag']);
    }

    // --- Composition ---

    public function testAllOfCompiled(): void
    {
        $result = $this->compileSchema(
            new OpenApi31Compiler(),
            new OA\Schema(
                schema: 'Dog',
                allOf: [
                    new OA\Schema(ref: '#/components/schemas/Pet'),
                    new OA\Schema(type: 'object', properties: [new OA\Property(property: 'breed', schema: new OA\Schema(type: 'string'))]),
                ],
            ),
        );

        $this->assertCount(2, $result['allOf']);
        $this->assertEquals('#/components/schemas/Pet', $result['allOf'][0]['$ref']);
        $this->assertArrayHasKey('properties', $result['allOf'][1]);
    }

    // --- Discriminator ---

    public function testDiscriminatorCompiled(): void
    {
        $result = $this->compileSchema(
            new OpenApi31Compiler(),
            new OA\Schema(
                schema: 'Pet',
                oneOf: [
                    new OA\Schema(ref: '#/components/schemas/Dog'),
                    new OA\Schema(ref: '#/components/schemas/Cat'),
                ],
                discriminator: new OA\Discriminator(propertyName: 'petType', mapping: ['dog' => '#/components/schemas/Dog']),
            ),
        );

        $this->assertEquals('petType', $result['discriminator']['propertyName']);
        $this->assertEquals(['dog' => '#/components/schemas/Dog'], $result['discriminator']['mapping']);
    }
}
