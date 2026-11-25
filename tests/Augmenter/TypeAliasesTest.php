<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Augmenter;
use OpenApi\Builder;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Tests\Concerns\ExpectsLogEntries;
use OpenApi\Tests\Fixtures;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;

final class TypeAliasesTest extends TestCase
{
    use ExpectsLogEntries;

    /**
     * A nested schema has no location of its own, so its notice names the nearest attribute
     * holding it that has one, however deep it sits.
     */
    public function testANestedAliasIsReportedAtItsNearestLocatedHolder(): void
    {
        $holder = Fixtures\Augmenter\NestedTypeAliases::class;
        $file = (string) (new \ReflectionClass($holder))->getFileName();
        $this->expectLogEntry("Schema type \"double\" is a classic alias, use \"number\" with format \"double\" (translation removed in 8.0; rector rule OpenApi\\Rector\\TypeAliasesRector rewrites it) in {$holder}::\$total in {$file}", 'notice');
        $this->expectLogEntry("Schema type \"float\" is a classic alias, use \"number\" with format \"float\" (translation removed in 8.0; rector rule OpenApi\\Rector\\TypeAliasesRector rewrites it) in {$holder} in {$file}:", 'notice');
        $this->expectLogEntry("Schema type \"int\" is a classic alias, use \"integer\" (translation removed in 8.0; rector rule OpenApi\\Rector\\TypeAliasesRector rewrites it) in {$holder} in {$file}:", 'notice');
        $this->allowLogEntry('Analysing source', 'Document is missing info', 'Required @OA\\Info() not found', 'Required @OA\\PathItem() not found');

        (new Builder())
            ->setMode(Builder\Mode::HYBRID)
            ->setLogger($this->trackingLogger())
            ->withAugmenters(static fn ($augmenters) => $augmenters->configure(['cleanup.enabled=false']))
            ->addSource($file)
            ->build();
    }

    /**
     * A schema built in code has no location anywhere, and its notice ends without one.
     */
    public function testAnAliasWithNoLocationEndsWithoutOne(): void
    {
        $specification = (new Specification())->add(new OA\Schema(
            properties: [new OA\Property(property: 'price', schema: new OA\Schema(type: 'float'))],
            component: 'Built',
        ));

        $logger = new class () extends AbstractLogger {
            /** @var list<string> */
            public array $messages = [];

            public function log($level, \Stringable|string $message, array $context = []): void
            {
                $this->messages[] = (string) $message;
            }
        };
        $augmenter = new Augmenter\TypeAliases();
        $augmenter->setLogger($logger);
        $augmenter($specification);

        $this->assertSame(['Schema type "float" is a classic alias, use "number" with format "float" (translation removed in 8.0; rector rule OpenApi\Rector\TypeAliasesRector rewrites it)'], $logger->messages);
    }
}
