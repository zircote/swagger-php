<?php declare(strict_types=1);

namespace OpenApi\Analysers;

use OpenApi\Analysis;
use OpenApi\Context;
use OpenApi\Generator;

interface AnalyserInterface
{
    public function setGenerator(Generator $generator): void;

    public function fromFile(string $filename, Context $context): Analysis;
}
