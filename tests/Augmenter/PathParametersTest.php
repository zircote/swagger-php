<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Augmenter;
use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Tests\Concerns\ExpectsLogEntries;
use OpenApi\Tests\Fixtures;
use PHPUnit\Framework\TestCase;

final class PathParametersTest extends TestCase
{
    use ExpectsLogEntries;

    public function testAPathParameterLeftUnsetIsRequired(): void
    {
        $component = new OA\Parameter(parameter: 'id', name: 'id', in: 'path');
        $nested = new OA\Parameter(name: 'slug', in: 'path');
        $spec = (new Specification())->add($component, new OA\Operation\Get(path: '/pets/{id}/{slug}', parameters: [$nested]));

        (new Augmenter\PathParameters())($spec);

        $this->assertTrue($component->required);
        $this->assertTrue($nested->required);
    }

    public function testLeavesWhatIsSetAndOtherLocationsAlone(): void
    {
        $explicit = new OA\Parameter(parameter: 'id', name: 'id', in: 'path', required: false);
        $query = new OA\Parameter(parameter: 'q', name: 'q', in: 'query');
        $ref = new OA\Parameter(in: 'path', ref: '#/components/parameters/id');
        $spec = (new Specification())->add($explicit, $query, new OA\Operation\Get(path: '/pets/{id}', parameters: [$ref]));

        (new Augmenter\PathParameters())($spec);

        $this->assertFalse($explicit->required);
        $this->assertNull($query->required);
        $this->assertNull($ref->required);
    }

    public function testANullableOrDefaultedPathParameterIsRequired(): void
    {
        $result = (new Builder())
            ->setMode(Mode::SPEC)
            ->setLogger($this->trackingLogger())
            ->addSource(new \ReflectionClass(Fixtures\Augmenter\PlainPathParameters::class))
            ->withSpecification(function (Specification $spec): void {
                $spec->add(new OA\Info(title: 'T', version: '1.0'));
            })
            ->build();

        $parameters = $result->toArray()['paths']['/pets/{id}/{slug}']['get']['parameters'];

        $this->assertSame([true, true], array_column($parameters, 'required'));
        $this->assertSame('integer', $parameters[0]['schema']['type']);
        $this->assertTrue($result->isValid());
    }
}
