<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Augmenter;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ParametersTest extends TestCase
{
    /**
     * @return iterable<string, array{OA\Parameter, string|null, string|null}>
     */
    public static function parameters(): iterable
    {
        yield 'a ref takes both from its component' => [new OA\Parameter(ref: '#/components/parameters/UserId'), 'id', 'path'];

        yield 'a ref wrapped in a Schema\Ref' => [new OA\Parameter(ref: new OA\Schema\Ref('#/components/parameters/UserId')), 'id', 'path'];

        yield 'what the parameter sets is kept' => [new OA\Parameter(name: 'user', ref: '#/components/parameters/UserId'), 'user', 'path'];

        yield 'a ref to nothing is left alone' => [new OA\Parameter(ref: '#/components/parameters/Nope'), null, null];

        yield 'an inline parameter is left alone' => [new OA\Parameter(name: 'page'), 'page', null];
    }

    #[DataProvider('parameters')]
    public function testFillsIdentityOnAnOperation(OA\Parameter $parameter, ?string $name, ?string $in): void
    {
        $specification = $this->specification(new OA\Operation\Get(path: '/users/{id}', parameters: [$parameter]));

        (new Augmenter\Parameters())($specification);

        $this->assertSame([$name, $in], [$parameter->name, $parameter->in]);
    }

    public function testFillsIdentityOnAPathItem(): void
    {
        $parameter = new OA\Parameter(ref: '#/components/parameters/UserId');
        $specification = $this->specification(new OA\PathItem(parameters: [$parameter]));

        (new Augmenter\Parameters())($specification);

        $this->assertSame(['id', 'path'], [$parameter->name, $parameter->in]);
    }

    protected function specification(OA\Operation|OA\PathItem $holder): Specification
    {
        return (new Specification())->add(
            new OA\Parameter\Path(parameter: 'UserId', name: 'id'),
            $holder,
        );
    }
}
