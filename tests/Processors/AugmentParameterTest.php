<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Processors;

use OpenApi\Analysers\TokenAnalyser;
use OpenApi\Generator;
use OpenApi\Processors\DocblockTrait;
use OpenApi\Tests\OpenApiTestCase;

class AugmentParameterTest extends OpenApiTestCase
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

    public function testExtractTags()
    {
        $mixed = $this->getContext(['comment' => <<<END
     /**
      * This is a summary.
     
      * This is a description
      *
      * @param string \$foo The foo parameter.
      */
END
        ]);
        $this->assertEquals('This is a summary.', $this->extractSummary($mixed->comment));
        $this->assertEquals('This is a description', $this->extractDescription($mixed->comment));
        $tags = [];
        $this->extractContent($mixed->comment, $tags);
        $this->assertEquals(['param' => ['foo' => ['type' => 'string', 'description' => 'The foo parameter.']]], $tags);
    }
}
