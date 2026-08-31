<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Utils;

use OpenApi\Augmenter\OperationIds;
use OpenApi\Augmenter\Tags;
use OpenApi\Augmenter\Types;
use OpenApi\Utils\CollectingLogger;
use OpenApi\Utils\PipeInterface;
use OpenApi\Utils\Pipeline;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PipelineTest extends TestCase
{
    public function testProcess(): void
    {
        $pipeline = new Pipeline([$this->pipe('x')]);

        $this->assertSame('x', $pipeline->process(''));
    }

    public static function configCases(): \Iterator
    {
        yield 'default' => [[], true];
        yield 'nested' => [['operationIds' => ['hash' => false]], false];
        yield 'dots-kv' => [['operationIds.hash' => false], false];
        yield 'dots-string' => [['operationIds.hash=false'], false];
    }

    #[DataProvider('configCases')]
    public function testConfigure(array $config, bool $expected): void
    {
        $pipeline = new Pipeline([new OperationIds(hash: true)]);

        $pipeline->configure($config);

        $operationIds = $pipeline->get(OperationIds::class);
        $rp = new \ReflectionProperty(OperationIds::class, 'hash');
        $this->assertInstanceOf(OperationIds::class, $operationIds);
        $this->assertEquals($expected, $rp->getValue($operationIds));
    }

    public function testGetConfigReportsPipeSettings(): void
    {
        $pipeline = new Pipeline([
            new OperationIds(hash: false),
            new Tags(whitelist: ['pets'], withDescription: false),
        ]);

        $this->assertSame([
            'operationIds' => ['hash' => false],
            'tags' => ['whitelist' => ['pets'], 'withDescription' => false],
        ], $pipeline->getConfig());
    }

    public function testGetConfigOmitsCollaborators(): void
    {
        // Types only exposes setTypeResolver() -- an object, so not config
        $pipeline = new Pipeline([new Types()]);

        $this->assertSame([], $pipeline->getConfig());
    }

    public function testGetConfigRoundTripsThroughConfigure(): void
    {
        $pipeline = new Pipeline([new OperationIds(hash: true)]);

        $this->assertSame(['operationIds' => ['hash' => true]], $pipeline->getConfig());

        // the keys getConfig() reports must be the keys configure() accepts
        $pipeline->configure($pipeline->getConfig());
        $this->assertSame(['operationIds' => ['hash' => true]], $pipeline->getConfig());

        $pipeline->configure(['operationIds.hash=false']);
        $this->assertSame(['operationIds' => ['hash' => false]], $pipeline->getConfig());
    }

    public static function unknownConfigCases(): iterable
    {
        yield 'unknown pipe' => [
            ['noSuchPipe.hash' => false],
            "Unknown config key 'noSuchPipe'; no matching pipe in this pipeline",
        ];
        yield 'unknown option' => [
            ['operationIds.noSuchOption' => false],
            "Unknown config option 'operationIds.noSuchOption'",
        ];
        yield 'stale classic key' => [
            ['operationId.hash' => false],
            "Unknown config key 'operationId'; no matching pipe in this pipeline",
        ];
    }

    #[DataProvider('unknownConfigCases')]
    public function testConfigureWarnsAboutUnknownConfig(array $config, string $expected): void
    {
        $logger = new CollectingLogger();
        $pipeline = new Pipeline([new OperationIds(hash: true)], logger: $logger);

        $pipeline->configure($config);

        $this->assertSame([['level' => 'warning', 'message' => $expected]], $logger->entries());
    }

    public function testConfigureIsQuietForKnownConfig(): void
    {
        $logger = new CollectingLogger();
        $pipeline = new Pipeline([new OperationIds(hash: true)], logger: $logger);

        $pipeline->configure(['operationIds.hash=false']);

        $this->assertSame([], $logger->entries());
        $this->assertSame(['operationIds' => ['hash' => false]], $pipeline->getConfig());
    }

    // --- Grouping ---

    public function testGroupOrderOverridesInsertionOrder(): void
    {
        $log = [];

        $resolve = $this->groupedPipe('resolve', $log);
        $augment = $this->groupedPipe('augment', $log);

        $pipeline = new Pipeline(
            [$augment, $resolve],
            groups: ['resolve', 'reduce', 'augment'],
            defaultGroup: 'augment',
        );

        $pipeline->process('ignored');

        $this->assertSame(['resolve', 'augment'], $log);
    }

    public function testDefaultGroupForPlainCallables(): void
    {
        $log = [];

        $pipeline = new Pipeline(
            [function ($p) use (&$log): void {
                $log[] = 'plain';
            }],
            groups: ['first', 'default'],
            defaultGroup: 'default',
        );

        $pipeline->process('x');

        $this->assertSame(['plain'], $log);
    }

    public function testEnumGroupsWork(): void
    {
        $log = [];

        $pipeline = new Pipeline(
            [$this->groupedPipe('resolve', $log), $this->groupedPipe('augment', $log)],
            groups: ['resolve', 'reduce', 'augment'],
            defaultGroup: 'augment',
        );

        $result = $pipeline->process('payload');

        $this->assertSame('payload', $result);
        $this->assertSame(['resolve', 'augment'], $log);
    }

    // --- No groups (BC) ---

    public function testNoGroupsUsesInsertionOrder(): void
    {
        $pipeline = new Pipeline([$this->pipe('a'), $this->pipe('b'), $this->pipe('c')]);

        $this->assertSame('abc', $pipeline->process(''));
    }

    protected function pipe(string $add): callable
    {
        return fn (string $payload): string => $payload . $add;
    }

    protected function groupedPipe(string $group, array &$log): PipeInterface
    {
        return new class ($group, $log) implements PipeInterface {
            public function __construct(
                protected string $group,
                protected array &$log,
            ) {
            }

            public function group(): string
            {
                return $this->group;
            }

            public function __invoke(mixed $payload): mixed
            {
                $this->log[] = $this->group;

                return null;
            }
        };
    }
}
