<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Assembler;
use OpenApi\Augmenter;
use OpenApi\Specification;
use OpenApi\Tests\Fixtures;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class OperationIdTest extends TestCase
{
    protected function assemble(string ...$classes): Specification
    {
        $assembler = new Assembler();
        foreach ($classes as $class) {
            $assembler->collect(new \ReflectionClass($class));
        }

        return $assembler->getSpecification();
    }

    #[DataProvider('operationIdProvider')]
    public function testGeneration(bool $hash): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\DocblockController::class);

        $augmenter = new Augmenter\OperationId(hash: $hash);
        $augmenter($spec);

        $operationId = $spec->operations[0]->operationId;
        $this->assertNotNull($operationId);

        if ($hash) {
            $this->assertMatchesRegularExpression('/^[a-f0-9]{32}$/', $operationId);
        } else {
            $this->assertStringContainsString('getThing', $operationId);
        }
    }

    public static function operationIdProvider(): \Generator
    {
        yield 'hashed' => [true];
        yield 'clear text' => [false];
    }

    public function testSkipsExplicit(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\DocblockController::class);
        $spec->operations[0]->operationId = 'custom';

        (new Augmenter\OperationId())($spec);

        $this->assertSame('custom', $spec->operations[0]->operationId);
    }
}
