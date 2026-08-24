<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Builder;

use OpenApi\Annotations as OA;
use OpenApi\Builder\Result;
use OpenApi\Specification;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ResultTest extends TestCase
{
    public static function validityCases(): iterable
    {
        yield 'no log' => [[], true];
        yield 'warning only' => [[['level' => 'warning', 'message' => 'meh']], true];
        yield 'notice only' => [[['level' => 'notice', 'message' => 'fyi']], true];
        yield 'error' => [[['level' => 'error', 'message' => 'boom']], false];
        yield 'error and warning' => [
            [['level' => 'warning', 'message' => 'meh'], ['level' => 'error', 'message' => 'boom']],
            false,
        ];
    }

    #[DataProvider('validityCases')]
    public function testIsValidOnlyErrorsCount(array $log, bool $expected): void
    {
        $result = Result::fromSpec(['a.php'], new Specification(), ['openapi' => '3.1.0'], $log);

        $this->assertSame($expected, $result->isValid());
    }

    #[DataProvider('validityCases')]
    public function testIsValidOnlyErrorsCountForClassic(array $log, bool $expected): void
    {
        $result = Result::fromClassic(['a.php'], new OA\OpenApi([]), $log);

        $this->assertSame($expected, $result->isValid());
    }

    public function testIsInvalidWhenNothingWasGenerated(): void
    {
        $this->assertFalse(Result::fromClassic(['a.php'], null)->isValid());
    }
}
