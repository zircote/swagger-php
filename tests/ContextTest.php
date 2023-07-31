<?php

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

use Swagger\Context;

class ContextTest extends SwaggerTestCase
{
    public function testDetect()
    {
        $context = Context::detect();
        $line = __LINE__ - 1;
        $this->assertSame('ContextTest', $context->class);
        $this->assertSame('\SwaggerTests\ContextTest', $context->fullyQualifiedName($context->class));
        $this->assertSame('testDetect', $context->method);
        $this->assertSame(__FILE__, $context->filename);
        $this->assertSame($line, $context->line);
        $this->assertSame('SwaggerTests', $context->namespace);
//        $this->assertCount(1, $context->uses); // Context::detect() doesn't pick up USE statements (yet)
    }

    public function testFullyQualifiedName()
    {
        $swagger = \Swagger\scan(__DIR__.'/Fixtures/Customer.php');
        $context = $swagger->definitions[0]->_context;
        // resolve with namespace
        $this->assertSame('\FullyQualified', $context->fullyQualifiedName('\FullyQualified'));
        $this->assertSame('\SwaggerFixures\Unqualified', $context->fullyQualifiedName('Unqualified'));
        $this->assertSame('\SwaggerFixures\Namespace\Qualified', $context->fullyQualifiedName('Namespace\Qualified'));
        // respect use statements
        $this->assertSame('\Exception', $context->fullyQualifiedName('Exception'));
        $this->assertSame('\SwaggerFixures\Customer', $context->fullyQualifiedName('Customer'));
        $this->assertSame('\Swagger\Logger', $context->fullyQualifiedName('Logger'));
        $this->assertSame('\Swagger\Logger', $context->fullyQualifiedName('lOgGeR')); // php has case-insensitive class names :-(
        $this->assertSame('\Swagger\Logger', $context->fullyQualifiedName('SwgLogger'));
        $this->assertSame('\Swagger\Annotations\QualifiedAlias', $context->fullyQualifiedName('SWG\QualifiedAlias'));
    }

    public function testPhpdocContent()
    {
        $singleLine = new Context(['comment' => <<<END
    /**
     * A single line.
     *
     * @SWG\Get(path="api/test1", @SWG\Response(response="200", description="a response"))
     */
END
        ]);
        $this->assertEquals('A single line.', $singleLine->phpdocContent());

        $multiline = new Context(['comment' => <<<END
/**
 * A description spread across
 * multiple lines.
 *           
 * even blank lines
 *
 * @SWG\Get(path="api/test1", @SWG\Response(response="200", description="a response"))
 */
END
        ]);
        $this->assertEquals("A description spread across\nmultiple lines.\n\neven blank lines", $multiline->phpdocContent());

        $escapedLinebreak = new Context(['comment' => <<<END
/**
 * A single line spread across \
 * multiple lines.
 *
 * @SWG\Get(path="api/test1", @SWG\Response(response="200", description="a response"))
 */
END
        ]);
        $this->assertEquals("A single line spread across multiple lines.", $escapedLinebreak->phpdocContent());
    }

    /**
     * https://phpdoc.org/docs/latest/guides/docblocks.html
     */
    public function testPhpdocSummaryAndDescription()
    {
        $single = new Context(['comment' => '/** This is a single line DocComment. */']);
        $this->assertEquals('This is a single line DocComment.', $single->phpdocContent());
        $multi = new Context(['comment' => "/**\n * This is a multi-line DocComment.\n */"]);
        $this->assertEquals('This is a multi-line DocComment.', $multi->phpdocContent());

        $emptyWhiteline = new Context(['comment' => <<<END
/**
 * This is a summary
 *
 * This is a description
 */
END
        ]);
        $this->assertEquals('This is a summary', $emptyWhiteline->phpdocSummary());
        $periodNewline = new Context(['comment' => <<<END
     /**
     * This is a summary.
     * This is a description
     */
END
        ]);
        $this->assertEquals('This is a summary.', $periodNewline->phpdocSummary());
        $multilineSummary = new Context(['comment' => <<<END
     /**
     * This is a summary
     * but this is part of the summary
     */
END
        ]);
    }
}
