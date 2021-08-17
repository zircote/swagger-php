<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Analysers;

use OpenApi\Context;

interface AnnotationFactoryInterface
{
    public function build(\Reflector $reflector, Context $context): array;
}
