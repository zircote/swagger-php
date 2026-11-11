<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\ComponentNamer;

use OpenApi\Augmenter\Generics;
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
        yield 'a class' => [['for' => Account::class], 'Author-for.Account'];
        yield 'type arguments' => [['of' => Account::class . ', int'], 'Author-of.Account_int'];
        yield 'nested type arguments' => [['of' => Author::class . '<' . Account::class . '>|null'], 'Author-of.Author-of.Account.-.or.null'];
        yield 'one argument per level' => [['of' => Author::class . '<' . Author::class . '<' . Account::class . '>>'], 'Author-of.Author-of.Author-of.Account'];
        yield 'a nested list followed by more arguments' => [['of' => Author::class . '<' . Account::class . '>, int'], 'Author-of.Author-of.Account.-_int'];
        yield 'a nested list holding both arguments' => [['of' => Author::class . '<' . Account::class . ', int>'], 'Author-of.Author-of.Account_int'];
        yield 'a union argument' => [['of' => Account::class . '|int, string'], 'Author-of.Account.or.int_string'];
        yield 'an intersection argument' => [['of' => Account::class . '&\\Countable'], 'Author-of.Account.and.Countable'];
        yield 'a list argument' => [['of' => 'list<' . Account::class . '>'], 'Author-of.list-of.Account'];
        yield 'a map argument' => [['of' => 'array<string, int>'], 'Author-of.array-of.string_int'];
        yield 'an integer range' => [['of' => 'int<-5, max>'], 'Author-of.int-of.-h5_max'];
        yield 'an array shape' => [['of' => 'array{id: int, name?: string}'], 'Author-of.shape-of.id.is.int_name.opt.string'];
        yield 'an unsealed array shape' => [['of' => 'array{id: int, ...}'], 'Author-of.shape-of.id.is.int_etc'];
        yield 'an array shape with extra types' => [['of' => 'array{id: int, ...<string, bool>}, int'], 'Author-of.shape-of.id.is.int_etc-of.string_bool.-.-_int'];
        yield 'type arguments beside another qualifier' => [['of' => Account::class, 'groups' => ['summary']], 'Author-groups.summary-of.Account'];
        yield 'type arguments last, after a qualifier that sorts after them' => [['units' => 'metric', 'of' => Author::class . '<' . Account::class . '>'], 'Author-units.metric-of.Author-of.Account'];
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
        $namer = new QualifiedName(strip: 'OpenApi\Tests\Fixtures\\', separator: '');

        $this->assertSame('ClassDescriptionsAuthor-for.ClassDescriptionsAccount', $namer->name($this->author(['for' => Account::class])));
    }

    public function testEscapesTheSeparatorsAClassNameUsesInTypeArguments(): void
    {
        $namer = new QualifiedName(strip: 'OpenApi\Tests\Fixtures\\');

        $this->assertSame('ClassDescriptions.Author-of.ClassDescriptions-dAccount', $namer->name($this->author(['of' => Account::class])));
        $this->assertSame('ClassDescriptions_Author-of.ClassDescriptions-uAccount', (new QualifiedName(strip: 'OpenApi\Tests\Fixtures\\', separator: '_'))->name($this->author(['of' => Account::class])));
    }

    public function testNamesATypeArgumentWithoutEscapesWithoutASeparator(): void
    {
        $namer = new QualifiedName(strip: 'OpenApi\Tests\Fixtures\\', separator: '');

        $this->assertSame('ClassDescriptionsAuthor-of.ClassDescriptionsAccount', $namer->name($this->author(['of' => Account::class])));
    }

    public function testWritesATypeKeywordInCamelCase(): void
    {
        $this->assertSame('Author-of.nonEmptyString', (new ShortName())->name($this->author(['of' => 'non-empty-string'])));
        $this->assertSame('Author-of.list-of.classString', (new ShortName())->name($this->author(['of' => 'list<class-string>'])));
    }

    /**
     * A character a name does not allow is escaped rather than replaced, so two keys stay apart.
     */
    public function testKeepsShapeKeysApart(): void
    {
        $namer = new ShortName();

        $this->assertSame('Author-of.shape-of.a-x20b.is.int', $namer->name($this->author(['of' => "array{'a b': int}"])));
        $this->assertSame('Author-of.shape-of.a-ub.is.int', $namer->name($this->author(['of' => 'array{a_b: int}'])));
    }

    public function testNamesAClassStringByItsClass(): void
    {
        $this->assertSame('Author-of.classString-of.Account', (new ShortName())->name($this->author(['of' => 'class-string<' . Account::class . '>'])));
    }

    public function testNamesACopyFromTheNamesGenericsLeftOnIt(): void
    {
        $schema = $this->author(['of' => Account::class])->setMeta(Generics::class, ['base' => 'Writer', 'arguments' => [Account::class => 'Holder']]);

        $this->assertSame('Writer-of.Holder', (new ShortName())->name($schema));
        $this->assertSame('Writer-of.Holder', (new QualifiedName())->name($schema));
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
