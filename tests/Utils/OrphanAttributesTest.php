<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Utils;

use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Spec as OA;
use OpenApi\Tests\Concerns\ExpectsLogEntries;
use OpenApi\Tests\Fixtures\OrphanAttributes;
use OpenApi\Utils\AttributeFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * An attribute on a member with no container of its own does not nest into another member's.
 */
final class OrphanAttributesTest extends TestCase
{
    use ExpectsLogEntries;

    /**
     * @return iterable<string, array{class-string, list<string>}>
     */
    public static function orders(): iterable
    {
        yield 'operation, then orphan' => [OrphanAttributes\OperationThenOrphan::class, ['get']];
        yield 'orphan first' => [OrphanAttributes\OrphanFirst::class, ['get']];
        yield 'two operations, then orphan' => [OrphanAttributes\TwoOperationsThenOrphan::class, ['get', 'post']];
    }

    /**
     * @param class-string $class
     * @param list<string> $methods
     */
    #[DataProvider('orders')]
    public function testAnOrphanIsDroppedAndEveryOperationKept(string $class, array $methods): void
    {
        $this->expectLogEntry('Response "422" ignored', 'warning');

        $document = $this->build($class)->toArray();

        $this->assertSame($methods, array_keys($document['paths']['/pets']));
        foreach ($document['paths']['/pets'] as $operation) {
            $this->assertArrayNotHasKey('422', $operation['responses']);
        }
    }

    public function testOneParametersAttributeIsNoContainerForAnothers(): void
    {
        $response = new OA\Response(response: 422, description: 'Invalid');
        $header = new OA\Header(header: 'X-Rate-Limit');

        $resolved = (new AttributeFactory())->resolveHierarchy([], [$response, $header]);

        $this->assertSame([$response, $header], $resolved);
        $this->assertNull($response->headers);
    }

    /**
     * @param class-string ...$classes
     */
    protected function build(string ...$classes): Builder\Result
    {
        return (new Builder())
            ->setMode(Mode::SPEC)
            ->setLogger($this->trackingLogger())
            ->withAugmenters(static fn ($augmenters) => $augmenters->configure(['cleanup.enabled=false']))
            ->addSource(array_map(static fn (string $class): \ReflectionClass => new \ReflectionClass($class), $classes))
            ->build();
    }
}
