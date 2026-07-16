<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Augmenter;
use OpenApi\Spec as OA;
use OpenApi\Tests\Concerns\AssemblesSpecification;
use OpenApi\Tests\Fixtures;
use PHPUnit\Framework\TestCase;

final class TagTest extends TestCase
{
    use AssemblesSpecification;

    public function testCreatesFromOperationUsage(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\TagController::class);

        (new Augmenter\Tags())($spec);

        $tagNames = array_map(fn (OA\Tag $t): ?string => $t->name, $spec->tags);
        $this->assertContains('alpha', $tagNames);
        $this->assertContains('beta', $tagNames);
    }

    public function testRemovesUnused(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\TagController::class);
        $spec->tags[] = new OA\Tag(name: 'unused');

        (new Augmenter\Tags())($spec);

        $tagNames = array_map(fn (OA\Tag $t): ?string => $t->name, $spec->tags);
        $this->assertNotContains('unused', $tagNames);
    }

    public function testWhitelistKeepsUnused(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\TagController::class);
        $spec->tags[] = new OA\Tag(name: 'unused');

        $augmenter = new Augmenter\Tags(whitelist: ['*']);
        $augmenter($spec);

        $tagNames = array_map(fn (OA\Tag $t): ?string => $t->name, $spec->tags);
        $this->assertContains('unused', $tagNames);
    }
}
