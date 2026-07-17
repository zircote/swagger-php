<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Concerns;

use OpenApi\Builder\Result;

trait AssertsBuilderResult
{
    public function assertBuilderResult(Result $result, int $warnCount = -1, int $errorCount = 0): void
    {
        $this->assertTrue($result->isValid());

        if ($warnCount > -1) {
            $this->assertCount($warnCount, $result->warnings(), '[Warning] ' . implode("\n[Warning] ", $result->warnings()));
        }

        if ($errorCount > -1) {
            $this->assertCount($errorCount, $result->errors(), '[Error] ' . implode("\n[Error] ", $result->errors()));
        }
    }
}
