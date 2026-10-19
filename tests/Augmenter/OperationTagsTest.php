<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Augmenter;
use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Specification;
use OpenApi\Tests\Concerns\ExpectsLogEntries;
use OpenApi\Tests\Fixtures;
use OpenApi\Utils\Pipeline;
use PHPUnit\Framework\TestCase;

final class OperationTagsTest extends TestCase
{
    use ExpectsLogEntries;

    public function testMethodAndClassTagsGoToTheirOperations(): void
    {
        $document = $this->build([Fixtures\Augmenter\TagHolder::class, Fixtures\Augmenter\TaggedController::class]);

        $this->assertSame(['list', 'admin', 'users'], $document['paths']['/users']['get']['tags']);
        $this->assertSame(['admin', 'users'], $document['paths']['/users/{id}']['delete']['tags']);
    }

    public function testRepeatedTagsFoldIntoOneDeclaration(): void
    {
        $document = $this->build([Fixtures\Augmenter\TagHolder::class, Fixtures\Augmenter\TaggedController::class]);

        $tags = array_column($document['tags'], null, 'name');
        $this->assertSame(['users', 'admin', 'list'], array_keys($tags));
        $this->assertSame(['name' => 'users', 'summary' => 'Users', 'description' => 'Everything about users'], $tags['users']);
        $this->assertSame(['name' => 'admin', 'description' => 'Needs an admin'], $tags['admin']);
    }

    public function testATagOnAClassWithoutOperationsOnlyDeclaresIt(): void
    {
        $document = $this->build([Fixtures\Augmenter\TagHolder::class, Fixtures\Augmenter\TagController::class]);

        $this->assertSame(['alpha', 'beta'], $document['paths']['/tagged']['get']['tags']);
    }

    public function testTagsSetDifferentlyAreReported(): void
    {
        $this->expectLogEntry('Tag "users" is declared more than once, folding into the last', 'warning');

        $document = $this->build([Fixtures\Augmenter\TagHolder::class, Fixtures\Augmenter\TaggedController::class, Fixtures\Augmenter\TagConflict::class]);

        $tags = array_column($document['tags'], null, 'name');
        $this->assertSame('Something else', $tags['users']['description']);
        $this->assertStringEndsWith('; description set differently', $this->recordedLogEntries()[0]['message']);
    }

    public function testAnInheritedOperationTakesTheChildsClassTags(): void
    {
        $document = $this->build([Fixtures\Augmenter\TagHolder::class, Fixtures\Augmenter\TaggedArchiveController::class]);

        $this->assertSame(['archived', 'Archive'], $document['paths']['/archive']['get']['tags']);
    }

    public function testHybridLeavesAClassicTagADeclaration(): void
    {
        $this->allowLogEntry('Analysing source');

        $document = (new Builder())
            ->setMode(Mode::HYBRID)
            ->setLogger($this->trackingLogger())
            ->addSource(__DIR__ . '/../Fixtures/Augmenter/ClassicTaggedController.php')
            ->build()
            ->toArray();

        $this->assertSame(['list'], $document['paths']['/users']['get']['tags']);
    }

    /**
     * The name-only tag on the method comes later, and leaves the description in place.
     */
    public function testHybridFoldsARepeatedClassicTag(): void
    {
        $this->allowLogEntry('Analysing source');

        $document = (new Builder())
            ->setMode(Mode::HYBRID)
            ->setLogger($this->trackingLogger())
            ->addSource(__DIR__ . '/../Fixtures/Augmenter/ClassicTaggedController.php')
            ->build()
            ->toArray();

        $this->assertSame([['name' => 'list', 'description' => 'Listings']], $document['tags']);
    }

    public function testRemovingTheAugmenterLeavesOperationsAsDeclared(): void
    {
        $document = $this->build(
            [Fixtures\Augmenter\TagHolder::class, Fixtures\Augmenter\TaggedController::class],
            fn (Pipeline $augmenters): Pipeline => $augmenters->remove(Augmenter\OperationTags::class),
        );

        $this->assertSame(['list'], $document['paths']['/users']['get']['tags']);
        $this->assertArrayNotHasKey('tags', $document['paths']['/users/{id}']['delete']);
    }

    /**
     * @param  list<class-string>                                                $classes
     * @param  (callable(Pipeline<Specification>): Pipeline<Specification>)|null $augmenters
     * @return array<string, mixed>
     */
    protected function build(array $classes, ?callable $augmenters = null): array
    {
        $builder = (new Builder())
            ->setMode(Mode::SPEC)
            // a tag's summary is new in 3.2
            ->setVersion('3.2.0')
            ->setLogger($this->trackingLogger());

        foreach ($classes as $class) {
            $builder->addSource(new \ReflectionClass($class));
        }

        if ($augmenters !== null) {
            $builder->withAugmenters($augmenters);
        }

        return $builder->build()->toArray();
    }
}
