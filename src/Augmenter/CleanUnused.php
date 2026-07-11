<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Utils\PipeInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Removes unreferenced components from the specification.
 *
 * Iterates multiple times to catch nested dependencies (a schema only
 * referenced by another unused schema should also be removed).
 *
 * @implements PipeInterface<Specification>
 */
class CleanUnused implements PipeInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected const MAX_ITERATIONS = 10;

    public function __construct(
        protected bool $enabled = true,
    ) {
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function group(): string|\BackedEnum
    {
        return Group::Reduce;
    }

    public function __invoke(mixed $payload): mixed
    {
        if (!$this->enabled) {
            return null;
        }

        for ($i = 0; $i < self::MAX_ITERATIONS; ++$i) {
            if (!$this->cleanup($payload)) {
                return null;
            }
        }

        $this->logger?->warning('CleanUnused: maximum iterations ({max}) reached, some unused components may remain', [
            'max' => self::MAX_ITERATIONS,
        ]);

        return null;
    }

    protected function cleanup(Specification $specification): bool
    {
        $usedRefs = [];
        $specification->getWalker()->eachRef(static function (OA\AbstractAttribute $a) use (&$usedRefs): void {
            $usedRefs[$a->ref] = true;
        });

        $removed = false;
        foreach ($this->componentDescriptors() as [$field, $refPrefix, $nameExtractor]) {
            if ($this->removeUnused($specification, $field, $refPrefix, $nameExtractor, $usedRefs)) {
                $removed = true;
            }
        }

        return $removed;
    }

    /**
     * @return list<array{string, string, \Closure}>
     */
    protected function componentDescriptors(): array
    {
        return [
            ['schemas', 'schemas', static fn (OA\Schema $s): ?string => $s->schema ?? $s->title],
            ['responses', 'responses', static fn (OA\Response $r): ?string => $r->response !== null ? (string) $r->response : null],
            ['parameters', 'parameters', static fn (OA\Parameter $p): ?string => $p->parameter ?? $p->name],
            ['requestBodies', 'requestBodies', static fn (OA\RequestBody $b): ?string => $b->request],
            ['headers', 'headers', static fn (OA\Header $h): ?string => $h->header],
            ['securitySchemes', 'securitySchemes', static fn (OA\Security\Scheme $s): ?string => $s->securityScheme],
            ['links', 'links', static fn (OA\Link $l): ?string => $l->link],
            ['examples', 'examples', static fn (OA\Example $e): ?string => $e->example],
        ];
    }

    /**
     * @param array<string, true> $usedRefs
     */
    protected function removeUnused(Specification $specification, string $field, string $refPrefix, \Closure $nameExtractor, array $usedRefs): bool
    {
        $removed = false;
        foreach ($specification->{$field} as $index => $item) {
            $name = $nameExtractor($item);
            if ($name !== null && !isset($usedRefs['#/components/' . $refPrefix . '/' . $name]) && !$this->isExplicit($item)) {
                unset($specification->{$field}[$index]);
                $removed = true;
            }
        }
        if ($removed) {
            $specification->{$field} = array_values($specification->{$field});
        }

        return $removed;
    }

    protected function isExplicit(OA\AbstractAttribute $item): bool
    {
        return $item->getReflector() instanceof \Reflector;
    }
}
