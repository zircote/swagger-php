<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Augmenter;
use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Spec as OA;
use OpenApi\Tests\Concerns\AssemblesSpecification;
use OpenApi\Tests\Concerns\ExpectsLogEntries;
use OpenApi\Tests\Fixtures\ContentSchema;
use OpenApi\Utils\Pipeline;
use PHPUnit\Framework\TestCase;

final class DefaultMediaTypeTest extends TestCase
{
    use AssemblesSpecification;
    use ExpectsLogEntries;

    public function testABareSchemaIsTheContentOfOneUntypedMediaType(): void
    {
        $schema = new OA\Schema\Ref('#/components/schemas/Pet');

        foreach ([new OA\Response(content: $schema), new OA\RequestBody(content: $schema)] as $holder) {
            $this->assertCount(1, $holder->content ?? []);
            $this->assertNull($holder->content[0]->mediaType, $holder::class);
            $this->assertSame($schema, $holder->content[0]->schema, $holder::class);
        }
    }

    public function testAnUntypedMediaTypeGetsTheDefault(): void
    {
        $spec = $this->assemble(ContentSchema\PetController::class);

        (new Augmenter\DefaultMediaType())($spec);

        foreach ($spec->operations as $operation) {
            foreach ([...($operation->responses ?? []), ...array_filter([$operation->requestBody])] as $holder) {
                $this->assertNotNull($holder->content[0]->mediaType ?? null);
            }
        }
        $this->assertSame('application/json', $spec->operations[0]->requestBody?->content[0]->mediaType);
    }

    public function testAnExplicitTypeIsLeftAlone(): void
    {
        $spec = $this->assemble(ContentSchema\PetController::class);

        (new Augmenter\DefaultMediaType('application/vnd.pets+json'))($spec);

        $list = $spec->operations[1]->responses[0]->content ?? [];
        $this->assertSame(['application/vnd.pets+json', 'application/xml'], array_map(static fn (OA\MediaType $mediaType): ?string => $mediaType->mediaType, $list));
    }

    public function testTheDocumentCarriesTheDefault(): void
    {
        $paths = $this->build()->toArray()['paths'];

        $this->assertSame(['application/json'], array_keys($paths['/pets']['post']['requestBody']['content']));
        $this->assertSame('#/components/schemas/Pet', $paths['/pets']['post']['responses'][200]['content']['application/json']['schema']['$ref']);
        $this->assertSame('object', $paths['/pets']['post']['responses'][400]['content']['application/json']['schema']['type']);
        $this->assertSame(['application/json', 'application/xml'], array_keys($paths['/pets']['get']['responses'][200]['content']));
    }

    public function testTheDefaultIsConfigurable(): void
    {
        $paths = $this->build(['defaultMediaType.mediaType=application/hal+json'])->toArray()['paths'];

        $this->assertSame(['application/hal+json'], array_keys($paths['/pets']['post']['requestBody']['content']));
        $this->assertSame(['application/hal+json', 'application/xml'], array_keys($paths['/pets']['get']['responses'][200]['content']));
    }

    /**
     * @param list<string> $config
     */
    protected function build(array $config = []): Builder\Result
    {
        return (new Builder())
            ->setMode(Mode::SPEC)
            ->setLogger($this->trackingLogger())
            ->withAugmenters(static fn (Pipeline $augmenters) => $augmenters->configure($config))
            ->addSource([
                new \ReflectionClass(ContentSchema\PetController::class),
                new \ReflectionClass(ContentSchema\Pet::class),
            ])
            ->build();
    }
}
