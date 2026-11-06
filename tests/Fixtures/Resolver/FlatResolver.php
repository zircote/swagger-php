<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Resolver;

use OpenApi\Assembler;
use OpenApi\Augmenter\Inheritance;
use OpenApi\Contracts\KeyResolverInterface;
use OpenApi\Resolver\Concerns\KeyResolverTrait;
use OpenApi\Spec as OA;
use OpenApi\Specification\ComponentKey;

/**
 * Describes the payment classes with the hierarchy flattened, as a resolver reading another
 * description of them does, and marks each schema so it keeps that shape.
 */
class FlatResolver implements KeyResolverInterface
{
    use KeyResolverTrait;

    public function resolveKey(ComponentKey $key, Assembler $assembler): bool
    {
        $schema = match ($key->class) {
            Payment::class => new OA\Schema(
                anyOf: [new OA\Schema(ref: CardPayment::class), new OA\Schema(ref: BankPayment::class)],
                discriminator: new OA\Discriminator(propertyName: 'type', mapping: ['card' => CardPayment::class, 'bank' => BankPayment::class]),
            ),
            CardPayment::class => $this->flat('number'),
            BankPayment::class => $this->flat('iban'),
            default => null,
        };
        if ($schema === null) {
            return false;
        }

        $schema->setReflector(new \ReflectionClass($key->class))->setMeta(Inheritance\Schemas::class, false);
        $assembler->getSpecification()->add($schema);

        return true;
    }

    protected function flat(string $own): OA\Schema
    {
        return new OA\Schema(type: 'object', properties: [
            new OA\Property(property: 'type', schema: new OA\Schema(type: 'string')),
            new OA\Property(property: $own, schema: new OA\Schema(type: 'string')),
        ]);
    }
}
