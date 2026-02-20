<?php declare(strict_types=1);

namespace OpenApi;

use OpenApi\Annotations as OA;

/**
 * @template T of OA\AbstractAnnotation
 */
interface ValidatorInterface
{
    /**
     * @param T $root
     */
    public function validate(Analysis $analysis, OA\AbstractAnnotation $root, \stdClass $context): bool;
}
