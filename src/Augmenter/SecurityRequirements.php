<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Rector\SecurityToRequirementRector;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Utils\PipeInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Drops security requirements written as a bare scheme name, and reports raw arrays.
 *
 * Classic reads `security: ['bearerAuth']` as `bearerAuth` with no scopes. Spec takes a
 * `Security\Requirement` per entry, so a string is reported and dropped. An operation left with
 * no requirements falls back to the ones above it, rather than declaring itself public with an
 * empty list.
 *
 * A raw array, `['bearerAuth' => []]` or `['bearerAuth']`, still compiles and is kept, but is
 * reported: the array form is removed in 8.0, and {@see SecurityToRequirementRector} rewrites it.
 *
 * Runs before `PathItems`, which clones path item requirements onto their operations.
 *
 * @implements PipeInterface<Specification>
 */
class SecurityRequirements implements PipeInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __invoke(mixed $payload): mixed
    {
        $this->checkHolder($payload->openapi);
        foreach ($payload->pathItems as $pathItem) {
            $this->checkHolder($pathItem);
        }
        foreach ($payload->operations as $operation) {
            $this->checkHolder($operation);
        }

        return null;
    }

    public function group(): string|\BackedEnum
    {
        return Group::Resolve;
    }

    protected function checkHolder(OA\OpenApi|OA\PathItem|OA\Operation $holder): void
    {
        if ($holder->security === null || $holder->security === []) {
            return;
        }

        $security = [];
        foreach ($holder->security as $item) {
            if (is_string($item)) {
                $this->logger?->warning(sprintf(
                    'Security requirement "%s" is a bare scheme name and was dropped, use new Security\Requirement(scheme: \'%s\') in %s',
                    $item,
                    $item,
                    $holder->getSourceLocation(),
                ));
                continue;
            }

            if (is_array($item)) {
                $this->logger?->warning(sprintf(
                    'Security requirement %s is a raw array, use Security\Requirement (array form removed in 8.0; rector rule %s rewrites it) in %s',
                    json_encode($item, JSON_UNESCAPED_SLASHES),
                    SecurityToRequirementRector::class,
                    $holder->getSourceLocation(),
                ));
            }

            $security[] = $item;
        }

        $holder->security = $security !== [] ? $security : null;
    }
}
