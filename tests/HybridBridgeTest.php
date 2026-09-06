<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Augmenter\Cleanup;
use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Tests\Concerns\AssertsSpecEquals;
use OpenApi\Utils\Pipeline;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Hybrid bridges classic annotations into the spec pipeline, so its document should be the
 * one classic produces. `ScratchTest` compares classic against spec and never runs hybrid,
 * which is why nested operations could go missing unnoticed.
 */
final class HybridBridgeTest extends TestCase
{
    use AssertsSpecEquals;

    public static function fixtures(): iterable
    {
        yield 'operation nested in a PathItem' => ['NestedPathItem'];
        yield 'operation nested in a Webhook' => ['NestedWebhook'];
    }

    #[DataProvider('fixtures')]
    public function testHybridMatchesClassic(string $fixture): void
    {
        $this->assertSpecEquals(
            $this->build($fixture, Mode::CLASSIC)->toYaml(),
            $this->build($fixture, Mode::HYBRID)->toYaml(),
        );
    }

    public function testWebhookOperationIsCollectedOnce(): void
    {
        $specification = $this->build('NestedWebhook', Mode::HYBRID)->specification();

        $this->assertCount(1, $specification->operations);
        $this->assertSame('newPet', $specification->operations[0]->webhook);
    }

    protected function build(string $fixture, Mode $mode): Builder\Result
    {
        // classic scans files, so hybrid does too — a reflector source yields nothing here
        return (new Builder())
            ->setMode($mode)
            ->addSource(__DIR__ . "/Fixtures/HybridBridge/{$fixture}.php")
            ->setVersion('3.1.0')
            // an unreferenced component would be dropped before the comparison
            ->withAugmenters(fn (Pipeline $augmenters): Pipeline => $augmenters->remove(Cleanup::class))
            ->build();
    }
}
