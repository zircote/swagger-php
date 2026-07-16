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
 * Resolves FQCN-based $ref values to JSON Reference paths.
 *
 * Builds a map of class names to their component paths and rewrites
 * any $ref that looks like a FQCN into the proper #/components/... path.
 *
 * @implements PipeInterface<Specification>
 */
class Refs implements PipeInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function group(): string|\BackedEnum
    {
        return Group::Resolve;
    }

    public function __invoke(mixed $payload): null
    {
        $refMap = $this->buildRefMap($payload);
        if ($refMap === []) {
            return null;
        }

        $this->resolveRefFQCNs($payload, $refMap);
        $this->resolveDiscriminatorMappings($payload, $refMap);

        return null;
    }

    protected function resolveRefFQCNs(Specification $specification, array $refMap): void
    {
        $unresolved = [];
        $specification->getWalker()->eachRef(function (OA\AbstractAttribute $attribute) use ($refMap, &$unresolved): void {
            if (str_starts_with($attribute->ref, '#/')) {
                return;
            }

            if (isset($refMap[$attribute->ref])) {
                $attribute->ref = $refMap[$attribute->ref];
            } else {
                $unresolved[$attribute->ref] = true;
            }
        });

        foreach (array_keys($unresolved) as $ref) {
            $this->logger?->debug('Ref: unresolved FQCN reference "' . $ref . '" — no matching component found');
        }

    }

    /**
     * Build a map of FQCN → #/components/{type}/{name}.
     *
     * @return array<string, string>
     */
    protected function buildRefMap(Specification $specification): array
    {
        $refMap = [];

        $specification->getWalker()->eachRef(function (OA\AbstractAttribute $attribute) use (&$refMap): void {
            if ($attribute->ref !== null) {
                $refMap[$attribute->getClassName()] = $attribute->ref;
            }
        });

        return $refMap;
    }

    /**
     * @param array<string, string> $refMap
     */
    protected function resolveDiscriminatorMappings(Specification $specification, array $refMap): void
    {
        foreach ($specification->schemas as $schema) {
            if (!$schema->discriminator instanceof OA\Discriminator || $schema->discriminator->mapping === null) {
                continue;
            }

            foreach ($schema->discriminator->mapping as $value => $type) {
                if (str_starts_with($type, '#/')) {
                    continue;
                }
                if (isset($refMap[$type])) {
                    $schema->discriminator->mapping[$value] = $refMap[$type];
                }
            }
        }
    }
}
