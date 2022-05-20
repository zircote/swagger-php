<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Analysers\TokenAnalyser;
use OpenApi\Context;
use OpenApi\Generator;

class ContextTest extends OpenApiTestCase
{
    public function testDetect(): void
    {
        $context = Context::detect();
        $line = __LINE__ - 1;
        $this->assertSame('ContextTest', $context->class);
        $this->assertSame('\\OpenApi\\Tests\\ContextTest', $context->fullyQualifiedName($context->class));
        $this->assertSame('testDetect', $context->method);
        $this->assertSame(__FILE__, $context->filename);
        $this->assertSame($line, $context->line);
        $this->assertSame('OpenApi\\Tests', $context->namespace);
    }

    public function testFullyQualifiedName(): void
    {
        $this->assertOpenApiLogEntryContains('Required @OA\PathItem() not found');
        $openapi = (new Generator($this->getTrackingLogger()))
            ->setAnalyser(new TokenAnalyser())
            ->generate([$this->fixture('Customer.php')]);
        $context = $openapi->components->schemas[0]->_context;
        // resolve with namespace
        $this->assertSame('\FullyQualified', $context->fullyQualifiedName('\FullyQualified'));
        $this->assertSame('\OpenApi\Tests\Fixtures\Unqualified', $context->fullyQualifiedName('Unqualified'));
        $this->assertSame('\OpenApi\Tests\Fixtures\Namespace\Qualified', $context->fullyQualifiedName('Namespace\Qualified'));
        // respect use statements
        $this->assertSame('\Exception', $context->fullyQualifiedName('Exception'));
        $this->assertSame('\OpenApi\Tests\Fixtures\Customer', $context->fullyQualifiedName('Customer'));
        $this->assertSame('\OpenApi\Generator', $context->fullyQualifiedName('Generator'));
        $this->assertSame('\OpenApi\Generator', $context->fullyQualifiedName('gEnerator')); // php has case-insensitive class names :-(
        $this->assertSame('\OpenApi\Generator', $context->fullyQualifiedName('OpenApiGenerator'));
        $this->assertSame('\OpenApi\Annotations\QualifiedAlias', $context->fullyQualifiedName('OA\QualifiedAlias'));
    }
}
