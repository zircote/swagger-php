<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Analysers;

use OpenApi\Analysis;
use OpenApi\Context;
use OpenApi\GeneratorAwareInterface;

interface AnalyserInterface extends GeneratorAwareInterface
{
    public function fromFile(string $filename, Context $context): Analysis;
}
