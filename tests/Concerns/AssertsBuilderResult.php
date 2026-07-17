<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Concerns;

use OpenApi\Builder\Result;

trait AssertsBuilderResult
{
    protected array $warningExcludes = [];

    protected array $errorExcludes = [];

    public function expectResultWarnings(array $warnings): void
    {
        $this->warningExcludes = $warnings;
    }

    public function expectResultErrors(array $errors): void
    {
        $this->errorExcludes = $errors;
    }

    public function assertBuilderResult(Result $result): void
    {
        $this->assertTrue($result->isValid());

        $filterByContains = (fn(array $list, array $patterns): array => array_filter($list, function (string $item) use ($patterns): bool {
            foreach ($patterns as $pattern) {
                if (str_contains($item, $pattern)) {
                    return false;
                }
            }

            return true;
        }));

        $warnings = $filterByContains($result->warnings(), $this->warningExcludes);
        $errors = $filterByContains($result->errors(), $this->errorExcludes);

        $this->assertCount(0, $warnings, '[Warning] ' . implode("\n[Warning] ", $result->warnings()));
        $this->assertCount(0, $errors, '[Error] ' . implode("\n[Error] ", $result->errors()));
    }
}
