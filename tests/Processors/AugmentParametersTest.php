<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Processors;

use OpenApi\Annotations\Operation;
use OpenApi\Annotations\Parameter;
use OpenApi\Annotations\PathItem;
use OpenApi\Generator;
use OpenApi\Processors\AugmentParameters;
use OpenApi\Processors\BuildPaths;
use OpenApi\Processors\Concerns\DocblockTrait;
use OpenApi\Processors\MergeIntoComponents;
use OpenApi\Processors\MergeIntoOpenApi;
use OpenApi\Tests\OpenApiTestCase;

class AugmentParametersTest extends OpenApiTestCase
{
    use DocblockTrait;

    public function testAugmentParameter(): void
    {
        $openapi = (new Generator())
            ->setAnalyser($this->getAnalyzer())
            ->generate([$this->fixture('UsingRefs.php')]);
        $this->assertCount(1, $openapi->components->parameters, 'OpenApi contains 1 reusable parameter specification');
        $this->assertEquals('ItemName', $openapi->components->parameters[0]->parameter, 'When no @OA\Parameter()->parameter is specified, use @OA\Parameter()->name');
    }

    public static function tagCases(): iterable
    {
        yield 'complete' => [
            '@param string $foo The foo parameter.',
            ['param' => ['foo' => ['type' => 'string', 'description' => 'The foo parameter.']]],
        ];

        yield 'no-description' => [
            '@param string $foo',
            ['param' => ['foo' => ['type' => 'string', 'description' => null]]],
        ];

        yield 'no-type' => [
            '@param $foo The description',
            ['param' => ['foo' => ['type' => null, 'description' => 'The description']]],
        ];

        yield 'no-var' => [
            '@param foo The description',
            ['param' => []],
        ];
    }

    /**
     * @dataProvider tagCases
     */
    public function testParseTags(string $params, array $expected): void
    {
        $mixed = $this->getContext(['comment' => "/**\n$params\n  *"]);
        $tags = [];
        $this->parseDocblock($mixed->comment, $tags);
        $this->assertEquals($expected, $tags);
    }

    /**
     * @requires PHP 8.1
     */
    public function testParameterNativeType(): void
    {
        $analysis = $this->analysisFromFixtures(['RequestUsingAttribute.php']);
        $analysis->process([
            new MergeIntoOpenApi(),
            new MergeIntoComponents(),
            new BuildPaths(),
            new AugmentParameters(),
        ]);

        $findPathItemByPath = function (string $path) use ($analysis): PathItem {
            foreach ($analysis->openapi->paths as $pathItem) {
                if ($pathItem->path === $path) {
                    return $pathItem;
                }
            }
            throw new \InvalidArgumentException('Not found');
        };
        $findParameterByName = function (string $name, Operation $operation): Parameter {
            foreach ($operation->parameters as $parameter) {
                if ($parameter->name === $name) {
                    return $parameter;
                }
            }
            throw new \InvalidArgumentException('Not found');
        };

        $pathItem = $findPathItemByPath('/get/{id}');
        $parameter = $findParameterByName('id', $pathItem->get);

        $this->assertEquals('integer', $parameter->schema->type);
    }
}
