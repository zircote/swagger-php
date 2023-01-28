<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Analysers;

use OpenApi\Annotations as OA;
use OpenApi\Context;
use OpenApi\Generator;

interface AnnotationFactoryInterface
{
    public function setGenerator(Generator $generator): void;

    /**
     * @return array<OA\AbstractAnnotation> top level annotations
     */
    public function build(\Reflector $reflector, Context $context): array;
}
