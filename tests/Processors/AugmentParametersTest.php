<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Processors;

use OpenApi\Generator;
use OpenApi\Processors\DocblockTrait;
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

    public function tagCases(): iterable
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
    public function testExtractTags(string $params, $expected)
    {
        $mixed = $this->getContext(['comment' => "/**\n$params\n  *"]);
        $tags = [];
        $this->extractContent($mixed->comment, $tags);
        $this->assertEquals($expected, $tags);
    }
}
