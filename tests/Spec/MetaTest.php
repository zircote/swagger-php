<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Spec;

use OpenApi\Spec as OA;
use PHPUnit\Framework\TestCase;

final class MetaTest extends TestCase
{
    public function testAnUnsetKeyReadsTheDefault(): void
    {
        $schema = new OA\Schema();

        $this->assertNull($schema->getMeta('adapter'));
        $this->assertSame('none', $schema->getMeta('adapter', 'none'));
    }

    public function testANullValueIsStillSet(): void
    {
        $schema = (new OA\Schema())->setMeta('adapter', null);

        $this->assertNull($schema->getMeta('adapter', 'none'));
    }

    /**
     * `Augmenter\PathItems` and `Augmenter\Inheritance` clone attributes; an entry set on the
     * clone must not appear on the original.
     */
    public function testACloneKeepsItsOwnEntries(): void
    {
        $schema = (new OA\Schema())->setMeta('adapter', 'symfony');

        $clone = (clone $schema)->setMeta('adapter', 'laravel');

        $this->assertSame('symfony', $schema->getMeta('adapter'));
        $this->assertSame('laravel', $clone->getMeta('adapter'));
    }

    /**
     * Classic's `Context` started as a place to carry information and became something the
     * processors branch on, one read at a time. `meta` is for code outside swagger-php, so a
     * reader or writer appearing in `src/` is a decision this test makes someone take on purpose.
     */
    public function testNothingInTheCoreUsesMeta(): void
    {
        $users = [];

        /** @var \SplFileInfo $file */
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(__DIR__ . '/../../src')) as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            if (preg_match('/->(get|set)Meta\(|->meta\b/', (string) file_get_contents($file->getPathname()))) {
                $users[] = $file->getBasename();
            }
        }

        sort($users);

        $this->assertSame(['AbstractAttribute.php'], $users, 'meta belongs to whoever extends swagger-php; core code using it is new behaviour');
    }
}
