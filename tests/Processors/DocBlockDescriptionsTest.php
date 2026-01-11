<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Processors;

use OpenApi\Annotations as OA;
use OpenApi\Generator;
use OpenApi\Processors\Concerns\DocblockTrait;
use OpenApi\Processors\DocBlockDescriptions;
use OpenApi\Tests\OpenApiTestCase;

class DocBlockDescriptionsTest extends OpenApiTestCase
{
    use DocblockTrait;

    public function testDocBlockDescription(): void
    {
        $analysis = $this->analysisFromFixtures(
            ['UsingPhpDoc.php'],
            $this->processorPipeline([
                new DocBlockDescriptions(),
            ])
        );

        $operations = $analysis->getAnnotationsOfType(OA\Operation::class);

        $this->assertSame('/api/test1', $operations[0]->path);
        $this->assertSame('Example summary.', $operations[0]->summary, 'Operation summary should be taken from phpDoc');
        $this->assertSame("Example description...\nMore description...", $operations[0]->description, 'Operation description should be taken from phpDoc');

        $this->assertSame('/api/test2', $operations[1]->path);
        $this->assertSame('Example summary.', $operations[1]->summary, 'Operation summary should be taken from phpDoc');
        $this->assertSame(Generator::UNDEFINED, $operations[1]->description, 'This operation only has summary in the phpDoc, no description');
    }

    public function testParseDocblockContent(): void
    {
        $singleLine = $this->getContext(['comment' => <<<END
    /**
     * A single line.
     *
     * @OA\Get(path="api/test1", @OA\Response(response="200", description="a response"))
     */
END
        ]);
        $this->assertEquals('A single line.', $this->parseDocblock($singleLine->comment));

        $multiline = $this->getContext(['comment' => <<<END
/**
 * A description spread across
 * multiple lines.
 *
 * even blank lines
 *
 * @OA\Get(path="api/test1", @OA\Response(response="200", description="a response"))
 */
END
        ]);
        $this->assertEquals("A description spread across\nmultiple lines.\n\neven blank lines", $this->parseDocblock($multiline->comment));

        $escapedLinebreak = $this->getContext(['comment' => <<<END
/**
 * A single line spread across \
 * multiple lines.
 *
 * @OA\Get(path="api/test1", @OA\Response(response="200", description="a response"))
 */
END
        ]);
        $this->assertEquals('A single line spread across multiple lines.', $this->parseDocblock($escapedLinebreak->comment));
    }

    /**
     * https://phpdoc.org/docs/latest/guides/docblocks.html.
     */
    public function testExtractDocblockSummaryAndDescription(): void
    {
        $single = $this->getContext(['comment' => '/** This is a single line DocComment. */']);
        $this->assertEquals('This is a single line DocComment.', $this->parseDocblock($single->comment));
        $multi = $this->getContext(['comment' => "/**\n * This is a multi-line DocComment.\n */"]);
        $this->assertEquals('This is a multi-line DocComment.', $this->parseDocblock($multi->comment));

        $emptyWhiteline = $this->getContext(['comment' => <<<END
    /**
     * This is a summary
     *
     * This is a description
     */
END
        ]);
        $this->assertEquals('This is a summary', $this->extractCommentSummary($this->parseDocblock($emptyWhiteline->comment)));
        $periodNewline = $this->getContext(['comment' => <<<END
     /**
     * This is a summary.
     * This is a description
     */
END
        ]);
        $this->assertEquals('This is a summary.', $this->extractCommentSummary($this->parseDocblock($periodNewline->comment)));
        $multilineSummary = $this->getContext(['comment' => <<<END
     /**
     * This is a summary
     * but this is part of the summary
     */
END
        ]);
        $this->assertEquals("This is a summary\nbut this is part of the summary", $this->parseDocblock($this->extractCommentSummary($multilineSummary->comment)));
    }
}
