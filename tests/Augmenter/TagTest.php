<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Assembler;
use OpenApi\Augmenter;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Tests\Fixtures;
use PHPUnit\Framework\TestCase;

final class TagTest extends TestCase
{
    protected function assemble(string ...$classes): Specification
    {
        $assembler = new Assembler();
        foreach ($classes as $class) {
            $assembler->collect(new \ReflectionClass($class));
        }

        return $assembler->getSpecification();
    }

    public function testCreatesFromOperationUsage(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\TagController::class);

        (new Augmenter\Tag())($spec);

        $tagNames = array_map(fn (OA\Tag $t): ?string => $t->name, $spec->tags);
        $this->assertContains('alpha', $tagNames);
        $this->assertContains('beta', $tagNames);
    }

    public function testRemovesUnused(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\TagController::class);
        $spec->tags[] = new OA\Tag(name: 'unused');

        (new Augmenter\Tag())($spec);

        $tagNames = array_map(fn (OA\Tag $t): ?string => $t->name, $spec->tags);
        $this->assertNotContains('unused', $tagNames);
    }

    public function testWhitelistKeepsUnused(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\TagController::class);
        $spec->tags[] = new OA\Tag(name: 'unused');

        $augmenter = new Augmenter\Tag(whitelist: ['*']);
        $augmenter($spec);

        $tagNames = array_map(fn (OA\Tag $t): ?string => $t->name, $spec->tags);
        $this->assertContains('unused', $tagNames);
    }
}
