<?php declare(strict_types=1);

namespace OpenApi\Analysers;

use OpenApi\Analysis;
use OpenApi\Context;

interface AnalyserInterface
{
    public function fromFile(string $filename, Context $context): Analysis;
}
