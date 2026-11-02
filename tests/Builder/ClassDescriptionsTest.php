<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Builder;

use OpenApi\Augmenter;
use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Specification\ComponentKey;
use OpenApi\Tests\Concerns\ExpectsLogEntries;
use OpenApi\Tests\Fixtures\ClassDescriptions\Account;
use OpenApi\Tests\Fixtures\ClassDescriptions\AccountController;
use OpenApi\Tests\Fixtures\ClassDescriptions\Article;
use OpenApi\Tests\Fixtures\ClassDescriptions\Author;
use OpenApi\Tests\Fixtures\ClassDescriptions\Statement;
use OpenApi\Utils\Pipeline;
use PHPUnit\Framework\TestCase;

/**
 * A class can be described more than once: a producer describing serialization groups contributes
 * one schema per group. A use of the class finds one of them by key, or none, and never one by chance.
 */
final class ClassDescriptionsTest extends TestCase
{
    use ExpectsLogEntries;

    public function testATypeNamingTwoSchemasIsLeftOut(): void
    {
        $this->expectLogEntry(Author::class . ' in ' . Article::class . '::$author has several schemas');
        // the editor asks for a light description, and there is none
        $this->allowLogEntry('unresolved reference');

        $document = $this->build(new ComponentKey(Author::class));

        $this->assertArrayNotHasKey('$ref', (array) $document['components']['schemas']['Article']['properties']['author']);
    }

    public function testADescriptionWithQualifiersLeavesTheClassToThePlainOne(): void
    {
        $document = $this->build(new ComponentKey(Author::class, ['groups' => ['light']]));

        $properties = $document['components']['schemas']['Article']['properties'];
        $this->assertSame('#/components/schemas/Author', $properties['author']['$ref']);
        $this->assertSame('#/components/schemas/AuthorLight', $properties['editor']['$ref']);
    }

    public function testAClassRefNamingTwoSchemasIsReported(): void
    {
        $this->expectLogEntry("Ref: ambiguous reference '" . Author::class . "{\"groups\":[\"light\"]}' — it names several schemas: Author, AuthorLight");
        // neither is plain, so the author's type names no schema
        $this->allowLogEntry('No schema for ' . Author::class);

        $document = $this->build(
            new ComponentKey(Author::class, ['groups' => ['light']]),
            new ComponentKey(Author::class, ['groups' => ['light']]),
        );

        $this->assertSame(Author::class, $document['components']['schemas']['Article']['properties']['editor']['$ref']);
    }

    public function testASchemaPropertyIsTypedWithTheClassSchemaNotItsResponse(): void
    {
        $document = (new Builder())
            ->setMode(Mode::SPEC)
            ->setLogger($this->trackingLogger())
            // nothing references the schemas from a path; keep them to assert on
            ->withAugmenters(fn (Pipeline $augmenters): Pipeline => $augmenters->remove(Augmenter\Cleanup::class))
            ->addSource(new \ReflectionClass(Account::class))
            ->addSource(new \ReflectionClass(Statement::class))
            ->withSpecification(fn (Specification $specification): Specification => $specification->add(new OA\Info(title: 'Class descriptions', version: '1.0.0')))
            ->build()
            ->toArray();

        $this->assertSame('#/components/schemas/Account', $document['components']['schemas']['Statement']['properties']['account']['$ref']);
    }

    public function testARequestBodyTypedWithAClassThatHasNoRequestBodyIsReported(): void
    {
        $this->expectLogEntry('No request body for ' . Account::class . ' in $account of ' . AccountController::class . "::open(), so the request body is left empty; its schema belongs in the request body's content");

        $document = (new Builder())
            ->setMode(Mode::SPEC)
            ->setLogger($this->trackingLogger())
            ->withAugmenters(fn (Pipeline $augmenters): Pipeline => $augmenters->remove(Augmenter\Cleanup::class))
            ->addSource(new \ReflectionClass(Account::class))
            ->addSource(new \ReflectionClass(AccountController::class))
            ->withSpecification(fn (Specification $specification): Specification => $specification->add(new OA\Info(title: 'Class descriptions', version: '1.0.0')))
            ->build()
            ->toArray();

        $this->assertArrayNotHasKey('$ref', (array) $document['paths']['/accounts']['post']['requestBody']);
    }

    /**
     * Contributes `Author` twice, as a full schema keyed by `$full` and a light one keyed by `$light`.
     *
     * @return array<string, mixed>
     */
    protected function build(ComponentKey $light, ?ComponentKey $full = null): array
    {
        return (new Builder())
            ->setMode(Mode::SPEC)
            ->setLogger($this->trackingLogger())
            // nothing references the schemas from a path; keep them to assert on
            ->withAugmenters(fn (Pipeline $augmenters): Pipeline => $augmenters->remove(Augmenter\Cleanup::class))
            ->addSource(new \ReflectionClass(Article::class))
            ->withSpecification(function (Specification $specification) use ($light, $full): void {
                $specification->add(new OA\Info(title: 'Class descriptions', version: '1.0.0'));

                foreach (['Author' => $full, 'AuthorLight' => $light] as $name => $key) {
                    $schema = new OA\Schema(type: 'object', component: $name);
                    $schema->setReflector(new \ReflectionClass(Author::class))->setComponentKey($key);
                    $specification->add($schema);
                }
            })
            ->build()
            ->toArray();
    }
}
