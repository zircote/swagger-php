<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Annotations as OA;
use OpenApi\Context;
use OpenApi\Generator;
use OpenApi\Tests\Fixtures\Customer;
use Psr\Log\NullLogger;

class ContextTest extends OpenApiTestCase
{
    public function testFullyQualifiedName(): void
    {
        $this->assertOpenApiLogEntryContains('Required @OA\PathItem() not found');
        $openapi = (new Generator($this->getTrackingLogger()))
            ->setAnalyser($this->getAnalyzer())
            ->setTypeResolver($this->getTypeResolver())
            ->generate([$this->fixture('Customer.php')]);
        $context = $openapi->components->schemas[0]->_context;
        // resolve with namespace
        $this->assertSame('\\FullyQualified', $context->fullyQualifiedName('\FullyQualified'));
        $this->assertSame('\\OpenApi\\Tests\\Fixtures\\Unqualified', $context->fullyQualifiedName('Unqualified'));
        $this->assertSame('\\OpenApi\\Tests\\Fixtures\\Namespace\Qualified', $context->fullyQualifiedName('Namespace\\Qualified'));
        // respect use statements
        $this->assertSame('\\Exception', $context->fullyQualifiedName('Exception'));
        $this->assertSame('\\OpenApi\\Tests\\Fixtures\\Customer', $context->fullyQualifiedName('Customer'));
        $this->assertSame('\\OpenApi\\Generator', $context->fullyQualifiedName('Generator'));
        $this->assertSame('\\OpenApi\\Generator', $context->fullyQualifiedName('gEnerator')); // php has case-insensitive class names :-(
        $this->assertSame('\\OpenApi\\Generator', $context->fullyQualifiedName('OpenApiGenerator'));
        $this->assertSame('\\OpenApi\\Annotations\\QualifiedAlias', $context->fullyQualifiedName('OA\\QualifiedAlias'));
    }

    public function testEnsureRoot(): void
    {
        $root = new Context(['logger' => new NullLogger(), 'version' => OA\OpenApi::VERSION_3_1_0]);
        $context = new Context(['logger' => $this->getTrackingLogger()]);

        // assert defaults set
        $this->assertNotInstanceOf(NullLogger::class, $context->logger);
        $this->assertEquals(OA\OpenApi::VERSION_3_0_0, $context->getVersion());

        $context->ensureRoot($root);

        // assert inheriting from root
        $this->assertInstanceOf(NullLogger::class, $context->logger);
        $this->assertEquals(OA\OpenApi::VERSION_3_1_0, $context->getVersion());
    }

    public function testDebugLocation(): void
    {
        $this->assertOpenApiLogEntryContains('Required @OA\PathItem() not found');
        $openapi = (new Generator($this->getTrackingLogger()))
            ->setTypeResolver($this->getTypeResolver())
            ->generate([$this->fixture('Customer.php')]);

        $customerSchema = $openapi->components->schemas[0];
        $this->assertStringContainsString(
            'Fixtures' . DIRECTORY_SEPARATOR . 'Customer.php on line ',
            $customerSchema->_context->getDebugLocation()
        );

        $customerPropertyFirstName = $customerSchema->properties[0];
        $this->assertStringContainsString(
            Customer::class . '->firstname in ',
            $customerPropertyFirstName->_context->getDebugLocation()
        );
    }

    public function testSerialize(): void
    {
        $context = new Context(['filename' => __FILE__], $this->getContext());
        $serialized = serialize($context);
        $unserialized = unserialize($serialized);

        $this->assertEquals($serialized, serialize($unserialized));
        $this->assertInstanceOf(Context::class, $unserialized->root());
        $this->assertNotSame($unserialized, $unserialized->root());
        $this->assertEquals(__FILE__, $unserialized->filename);
    }
}
