<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\Tests\Docs;

use OpenApi\Tools\Docs\DocGenerator;

/**
 * Exposes the two protected helpers the reference's type rendering depends on.
 */
class TestDocGenerator extends DocGenerator
{
    /**
     * @return array<string, mixed>
     */
    public function generate(): array
    {
        return [];
    }

    /**
     * @return list<string>
     */
    public function split(string $type): array
    {
        return $this->splitUnion($type);
    }

    /**
     * @param array{aliases?: array<string, string>, imports?: array<string, array{name: string, from: string}>} $doc
     * @param \ReflectionClass<object>                                                                           $rc
     */
    public function expand(string $type, array $doc, \ReflectionClass $rc): string
    {
        return $this->expandTypeAliases($type, $doc, $rc);
    }
}
