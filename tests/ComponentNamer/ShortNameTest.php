<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\ComponentNamer;

use OpenApi\ComponentNamer\QualifiedName;
use OpenApi\ComponentNamer\ShortName;
use OpenApi\Spec as OA;
use OpenApi\Specification\ComponentKey;
use OpenApi\Tests\Fixtures\ClassDescriptions\Account;
use OpenApi\Tests\Fixtures\ClassDescriptions\Author;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ShortNameTest extends TestCase
{
    /**
     * @return iterable<string, array{array<string, mixed>, string}>
     */
    public static function qualifierCases(): iterable
    {
        yield 'plain' => [[], 'Author'];
        yield 'one value' => [['groups' => ['summary']], 'Author-groups.summary'];
        yield 'values in sorted order' => [['groups' => ['summary', 'admin']], 'Author-groups.admin_summary'];
        yield 'qualifiers in sorted order' => [['groups' => 'summary', 'direction' => 'input'], 'Author-direction.input-groups.summary'];
        yield 'no values' => [['groups' => []], 'Author-groups'];
        yield 'scalars' => [['strict' => true, 'version' => 2], 'Author-strict.true-version.2'];
        yield 'characters a name does not allow' => [['groups' => ['user:read']], 'Author-groups.user_read'];
        yield 'a class' => [['of' => Account::class], 'Author-of.Account'];
    }

    /**
     * @param array<string, mixed> $qualifiers
     */
    #[DataProvider('qualifierCases')]
    public function testAddsTheKeysQualifiers(array $qualifiers, string $expected): void
    {
        $this->assertSame($expected, (new ShortName())->name($this->author($qualifiers)));
    }

    public function testNamesAClassQualifierLikeItsComponent(): void
    {
        $namer = new QualifiedName(strip: 'OpenApi\Tests\Fixtures\\');

        $this->assertSame('ClassDescriptions.Author-of.ClassDescriptions.Account', $namer->name($this->author(['of' => Account::class])));
    }

    public function testLeavesAComponentWithoutANameInline(): void
    {
        $schema = (new OA\Schema())->setComponentKey(new ComponentKey(Author::class, ['groups' => ['summary']]));

        $this->assertNull((new ShortName())->name($schema));
    }

    /**
     * @param array<string, mixed> $qualifiers
     */
    protected function author(array $qualifiers): OA\Schema
    {
        return (new OA\Schema())
            ->setReflector(new \ReflectionClass(Author::class))
            ->setComponentKey(new ComponentKey(Author::class, $qualifiers));
    }
}
