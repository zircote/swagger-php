<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Utils;

use OpenApi\Utils\JsonPointer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class JsonPointerTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function tokens(): iterable
    {
        yield 'nothing to escape' => ['plain', 'plain'];
        yield 'slash' => ['a/b', 'a~1b'];
        yield 'tilde' => ['a~b', 'a~0b'];
        yield 'both' => ['Odd/Name~With', 'Odd~1Name~0With'];
        yield 'empty' => ['', ''];

        // a name that already looks like an escape has to survive the round trip
        yield 'literal ~1' => ['~1', '~01'];
        yield 'literal ~0' => ['~0', '~00'];
        yield 'literal ~1 inside' => ['a~1b', 'a~01b'];
    }

    public function testRefEscapesEveryToken(): void
    {
        $this->assertSame(
            '#/components/schemas/Odd~1Name~0With',
            JsonPointer::ref('components', 'schemas', 'Odd/Name~With')
        );
    }

    public function testRefTreatsEachArgumentAsOneToken(): void
    {
        $this->assertSame('#/a~1b/c', JsonPointer::ref('a/b', 'c'));
        $this->assertSame('#/a/b/c', JsonPointer::ref('a', 'b', 'c'));
    }

    #[DataProvider('tokens')]
    public function testEncode(string $raw, string $encoded): void
    {
        $this->assertSame($encoded, JsonPointer::encode($raw));
    }

    #[DataProvider('tokens')]
    public function testDecodeReversesEncode(string $raw, string $encoded): void
    {
        $this->assertSame($raw, JsonPointer::decode($encoded));
    }
}
