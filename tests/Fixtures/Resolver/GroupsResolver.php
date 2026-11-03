<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Resolver;

use OpenApi\Assembler;
use OpenApi\Contracts\KeyResolverInterface;
use OpenApi\Resolver\Concerns\KeyResolverTrait;
use OpenApi\Spec as OA;
use OpenApi\Specification\ComponentKey;

/**
 * Describes a class once per set of serialization groups, and records every key it was offered.
 */
class GroupsResolver implements KeyResolverInterface
{
    use KeyResolverTrait;

    /**
     * @var array<string, int>
     */
    public array $attempts = [];

    public function resolveKey(ComponentKey $key, Assembler $assembler): bool
    {
        $this->attempts[(string) $key] = ($this->attempts[(string) $key] ?? 0) + 1;

        $groups = (array) ($key->qualifiers['groups'] ?? []);
        if ($groups === []) {
            return false;
        }

        $class = new \ReflectionClass($key->class);
        $schema = new OA\Schema(
            type: 'object',
            component: $class->getShortName() . implode('', array_map(ucfirst(...), array_map(strval(...), $groups))),
        );
        $assembler->getSpecification()->add($schema->setReflector($class)->setComponentKey($key));

        return true;
    }
}
