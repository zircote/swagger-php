<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Utils;

use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Spec as OA;
use OpenApi\Tests\Concerns\ExpectsLogEntries;
use OpenApi\Tests\Fixtures\MemberErrors;
use PHPUnit\Framework\TestCase;

/**
 * A member that fails assembly costs that member, not its class or the build.
 */
final class MemberErrorsTest extends TestCase
{
    use ExpectsLogEntries;

    public function testAnAmbiguousMethodIsLeftOutAndTheClassKept(): void
    {
        $this->expectLogEntry('Method ' . MemberErrors\PetController::class . '::change() cannot be read, skipped: Ambiguous hierarchy', 'error');

        $document = $this->build(MemberErrors\PetController::class)->toArray();

        $this->assertSame('Member errors', $document['info']['title']);
        $this->assertSame(['/pets'], array_keys($document['paths']));
    }

    /**
     * The inherited method is read again by `Augmenter\Inheritance\Operations`, outside assembly.
     */
    public function testAnAmbiguousInheritedMethodDoesNotAbortTheBuild(): void
    {
        $this->expectLogEntry('Method ' . MemberErrors\AbstractController::class . '::change() cannot be read, skipped: Ambiguous hierarchy', 'error');
        $this->expectLogEntry('Method ' . MemberErrors\PetController::class . '::change() cannot be read, skipped: Ambiguous hierarchy', 'error');

        $document = $this->build(MemberErrors\InheritedController::class, MemberErrors\PetController::class)->toArray();

        $this->assertSame(['/pets', '/v1/items'], array_keys($document['paths']));
    }

    public function testAPropertyThatFailsIsLeftOutAndItsSchemaKept(): void
    {
        $this->expectLogEntry('Property ' . MemberErrors\Pet::class . ('::$tags cannot be read, skipped: Property ' . OA\Type::class . '::String in properties needs a string key'), 'error');
        $this->allowLogEntry('Document is missing info');

        $schemas = $this->build(MemberErrors\Pet::class)->toArray()['components']['schemas'];

        $this->assertSame(['name'], array_keys($schemas['Pet']['properties']));
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
