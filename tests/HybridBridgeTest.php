<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Augmenter\Cleanup;
use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Loggers\CollectingLogger;
use OpenApi\Tests\Concerns\AssertsSpecEquals;
use OpenApi\Utils\Pipeline;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Hybrid bridges classic annotations into the spec pipeline, so its document should be the
 * one classic produces. `ScratchTest` compares classic against spec and never runs hybrid,
 * which is why nested operations could go missing unnoticed.
 */
final class HybridBridgeTest extends TestCase
{
    use AssertsSpecEquals;

    /**
     * @return iterable<mixed>
     */
    public static function fixtures(): iterable
    {
        yield 'operation nested in a PathItem' => ['NestedPathItem'];
        yield 'operation nested in a Webhook' => ['NestedWebhook'];
        yield 'example and webhook declared on a class' => ['ClassLevelComponents'];
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

    /**
     * Hybrid runs neither classic's merge processors nor its `validate()`, so it reports what it
     * cannot place itself: at least what classic does.
     */
    public function testHybridReportsMisplacedAnnotationsAsClassicDoes(): void
    {
        // several classes in one file, so none of them autoloads
        require_once __DIR__ . '/Fixtures/HybridBridge/MisplacedAttributes.php';

        $unexpected = [];
        foreach ([Mode::CLASSIC, Mode::HYBRID] as $mode) {
            $logger = new CollectingLogger();
            $this->build('MisplacedAttributes', $mode, $logger);
            $messages = array_values(array_filter(
                array_column($logger->entries(), 'message'),
                fn (string $message): bool => str_starts_with($message, 'Unexpected ')
            ));
            sort($messages);
            $unexpected[$mode->name] = $messages;
        }

        $this->assertCount(2, $unexpected['CLASSIC']);
        $this->assertSame([], array_diff($unexpected['CLASSIC'], $unexpected['HYBRID']));

        // classic drops a misnested docblock annotation without a word (#1151); hybrid does not
        $this->assertCount(3, $unexpected['HYBRID']);
        $this->assertStringContainsString('on line 53', $unexpected['HYBRID'][1]);
    }

    /**
     * A nested property inherits its parent's reflector in classic, which would give it the
     * class docblock. Hybrid follows spec here: the schemas take it, their properties do not.
     */
    public function testNestedPropertiesDoNotTakeTheClassDocblock(): void
    {
        $schemas = $this->build('NestedProperties', Mode::HYBRID)->toArray()['components']['schemas'];

        foreach (['First' => ['a', 'b'], 'Second' => ['c']] as $name => $properties) {
            $this->assertSame('Describes the schemas, not their properties.', $schemas[$name]['description']);
            foreach ($properties as $property) {
                $this->assertArrayNotHasKey('description', $schemas[$name]['properties'][$property], "{$name}.{$property}");
            }
        }
    }

    protected function build(string $fixture, Mode $mode, ?LoggerInterface $logger = null): Builder\Result
    {
        // classic scans files, so hybrid does too — a reflector source yields nothing here
        return (new Builder())
            ->setMode($mode)
            ->setLogger($logger ?? new NullLogger())
            ->addSource(__DIR__ . "/Fixtures/HybridBridge/{$fixture}.php")
            ->setVersion('3.1.0')
            // an unreferenced component would be dropped before the comparison
            ->withAugmenters(fn (Pipeline $augmenters): Pipeline => $augmenters->remove(Cleanup::class))
            ->build();
    }
}
