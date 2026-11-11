<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Builder;

use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Loggers\CollectingLogger;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Tests\Fixtures\PluginDiscovery\AlphaPlugin;
use OpenApi\Tests\Fixtures\PluginDiscovery\BetaPlugin;
use OpenApi\Tests\Fixtures\PluginDiscovery\GammaPlugin;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Packages declare plugins in their `composer.json`, and a builder asked to discover them runs them
 * in a fixed order, after the plugins it was given.
 */
final class PluginDiscoveryTest extends TestCase
{
    private const FIXTURES = __DIR__ . '/../Fixtures/PluginDiscovery';

    public function testAPackagesPluginsRunAfterThoseOfThePackagesItRequires(): void
    {
        // acme/alpha requires acme/beta, so beta runs first although alpha sorts first by name
        $this->assertSame(
            ['https://beta.example', 'https://alpha.example', 'https://gamma.example'],
            $this->servers(fn (Builder $builder): Builder => $builder->withDiscoveredPlugins(vendorDir: self::FIXTURES . '/ordered'))
        );
    }

    public function testEachDiscoveredPluginIsLogged(): void
    {
        $logger = new CollectingLogger();

        (new Builder($logger))->withDiscoveredPlugins(vendorDir: self::FIXTURES . '/ordered');

        $this->assertSame([
            'Plugin "' . BetaPlugin::class . '" discovered in package "acme/beta"',
            'Plugin "' . AlphaPlugin::class . '" discovered in package "acme/alpha"',
            'Plugin "' . GammaPlugin::class . '" discovered in package "acme/gamma"',
        ], array_column($logger->entries(), 'message'));
        $this->assertSame(['info'], array_values(array_unique(array_column($logger->entries(), 'level'))));
    }

    public function testAPluginListedFirstKeepsItsSettingsAndPosition(): void
    {
        $this->assertSame(
            ['https://listed.example', 'https://beta.example', 'https://gamma.example'],
            $this->servers(fn (Builder $builder): Builder => $builder
                ->withPlugins([AlphaPlugin::class => ['url' => 'https://listed.example']])
                ->withDiscoveredPlugins(vendorDir: self::FIXTURES . '/ordered'))
        );
    }

    public function testAnExcludedPluginIsLeftOut(): void
    {
        $this->assertSame(
            ['https://beta.example', 'https://alpha.example'],
            $this->servers(fn (Builder $builder): Builder => $builder->withDiscoveredPlugins([GammaPlugin::class], self::FIXTURES . '/ordered'))
        );
    }

    public function testAPluginListedAfterDiscoveryIsSkipped(): void
    {
        $logger = new CollectingLogger();

        $servers = $this->servers(fn (Builder $builder): Builder => $builder
            ->withDiscoveredPlugins(vendorDir: self::FIXTURES . '/ordered')
            ->withPlugins([BetaPlugin::class => ['url' => 'https://late.example']]), $logger);

        $this->assertSame(['https://beta.example', 'https://alpha.example', 'https://gamma.example'], $servers);
        $this->assertContains(
            'Plugin "' . BetaPlugin::class . '" was added by discovery already, skipped; list it before withDiscoveredPlugins() to give it settings or a position',
            array_column($logger->entries(), 'message')
        );
    }

    public function testNothingIsDiscoveredUnlessAsked(): void
    {
        $this->assertSame([], $this->servers(fn (Builder $builder): Builder => $builder));
    }

    public function testComposerOnesListIsRead(): void
    {
        $this->assertSame(
            ['https://beta.example'],
            $this->servers(fn (Builder $builder): Builder => $builder->withDiscoveredPlugins(vendorDir: self::FIXTURES . '/composer1'))
        );
    }

    public function testTheInstallationSwaggerPhpRunsFromIsReadByDefault(): void
    {
        $logger = new CollectingLogger();

        (new Builder($logger))->withDiscoveredPlugins();

        // found and read; whether a package there declares a plugin is up to this repository's dependencies
        $this->assertSame([], array_filter($logger->entries(), static fn (array $entry): bool => in_array($entry['level'], ['warning', 'error'], true)));
    }

    public function testAListedPluginIsRecognisedWhateverItsCaseOrLeadingBackslash(): void
    {
        $this->assertSame(
            ['https://listed.example', 'https://alpha.example'],
            $this->servers(fn (Builder $builder): Builder => $builder
                ->withPlugins([BetaPlugin::class => ['url' => 'https://listed.example']])
                ->withDiscoveredPlugins(['\\' . strtoupper(GammaPlugin::class)], self::FIXTURES . '/case'))
        );
    }

    /**
     * @return iterable<string, array{string, string, string}>
     */
    public static function problems(): iterable
    {
        yield 'no installed.json' => ['missing', 'warning', 'to discover plugins'];
        yield 'not JSON' => ['unreadable', 'warning', 'to discover plugins: Syntax error'];
        yield 'plugins not a list' => ['invalid', 'warning', 'Package "acme/not-a-list" declares swagger-php plugins that are not a list of class names, skipped'];
        yield 'a class that does not exist' => ['invalid', 'error', 'Plugin "Acme\Missing\Plugin" does not exist, declared by package "acme/missing-class", skipped'];
    }

    #[DataProvider('problems')]
    public function testProblemsAreLoggedAndTheRestRuns(string $vendorDir, string $level, string $message): void
    {
        $logger = new CollectingLogger();

        $servers = $this->servers(fn (Builder $builder): Builder => $builder->withDiscoveredPlugins(vendorDir: self::FIXTURES . '/' . $vendorDir), $logger);

        $matching = array_filter($logger->entries(), static fn (array $entry): bool => str_contains($entry['message'], $message));
        $this->assertCount(1, $matching);
        $this->assertSame($level, array_values($matching)[0]['level']);
        $this->assertSame($vendorDir === 'invalid' ? ['https://gamma.example'] : [], $servers);
    }

    /**
     * The URLs of the servers the plugins contributed, in the order they ran.
     *
     * @param callable(Builder): Builder $configure
     *
     * @return list<string|null>
     */
    private function servers(callable $configure, ?CollectingLogger $logger = null): array
    {
        $specification = new Specification();

        $configure((new Builder($logger))->setMode(Mode::SPEC)->setSources([]))
            ->withSpecification(function (Specification $assembled) use (&$specification): void {
                $specification = $assembled;
            })
            ->build();

        return array_map(static fn (OA\Server $server): ?string => $server->url, $specification->servers);
    }
}
