<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Specification;

use OpenApi\OpenApiException;
use OpenApi\Spec as OA;
use OpenApi\Specification\ComponentKey;
use OpenApi\Tests\Fixtures\ClassDescriptions\Article;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ComponentKeyTest extends TestCase
{
    public function testQualifiersAreSortedByName(): void
    {
        $key = new ComponentKey('App\Author', ['version' => 2, 'groups' => ['light']]);

        $this->assertSame(['groups', 'version'], array_keys($key->qualifiers));
        $this->assertTrue($key->equals(new ComponentKey('App\Author', ['groups' => ['light'], 'version' => 2])));
    }

    public function testTypeArgumentsComeLast(): void
    {
        $key = new ComponentKey('App\\Envelope', ['units' => 'metric', 'of' => 'App\\Customer', 'groups' => ['admin']]);

        $this->assertSame(['groups', 'units', 'of'], array_keys($key->qualifiers));
        $this->assertTrue($key->equals(new ComponentKey('App\\Envelope', ['of' => 'App\\Customer', 'groups' => ['admin'], 'units' => 'metric'])));
    }

    public function testAListIsASet(): void
    {
        $key = new ComponentKey('App\Author', ['groups' => ['light', 'admin']]);

        $this->assertSame(['admin', 'light'], $key->qualifiers['groups']);
        $this->assertSame('App\Author{"groups":["admin","light"]}', (string) $key);
    }

    public function testAKeyWithoutQualifiersIsTheClass(): void
    {
        $key = new ComponentKey('App\Author');

        $this->assertTrue($key->isPlain());
        $this->assertSame('App\Author', (string) $key);
        $this->assertFalse($key->equals(new ComponentKey('App\Author', ['groups' => ['light']])));
    }

    /**
     * @return iterable<string, array{array<string, mixed>}>
     */
    public static function invalidQualifiers(): iterable
    {
        yield 'a map as a value' => [['context' => ['groups' => ['light']]]];
        yield 'a map in a list' => [['groups' => ['light' => true]]];
        yield 'an object' => [['type' => new \stdClass()]];
        yield 'null' => [['groups' => null]];
    }

    /**
     * @param array<string, mixed> $qualifiers
     */
    #[DataProvider('invalidQualifiers')]
    public function testQualifiersAreOneLevelDeep(array $qualifiers): void
    {
        $this->expectException(OpenApiException::class);

        new ComponentKey('App\Author', $qualifiers);
    }

    public function testAComponentOnAClassIsKeyedByTheClass(): void
    {
        $schema = (new OA\Schema())->setReflector(new \ReflectionClass(Article::class));

        $this->assertSame(Article::class, (string) $schema->getComponentKey());
    }

    public function testAComponentOnAMemberHasNoKey(): void
    {
        $schema = (new OA\Schema())->setReflector(new \ReflectionProperty(Article::class, 'author'));

        $this->assertNull($schema->getComponentKey());
    }

    public function testAKeySetByHandWins(): void
    {
        $key = new ComponentKey(Article::class, ['groups' => ['light']]);
        $schema = (new OA\Schema())->setReflector(new \ReflectionClass(Article::class))->setComponentKey($key);

        $this->assertSame($key, $schema->getComponentKey());
    }

    public function testARefReadsTypeArgumentsInOneSpelling(): void
    {
        $key = (new OA\Schema\Ref(ref: Article::class, qualifiers: ['of' => 'string,\\' . Article::class]))->getRefKey();

        $this->assertInstanceOf(ComponentKey::class, $key);
        $this->assertSame('string, ' . Article::class, $key->qualifiers['of']);
    }

    /**
     * A class nested in an argument loses its leading `\` too, as the name it gets has none.
     */
    public function testARefReadsNestedTypeArgumentsInOneSpelling(): void
    {
        $written = (new OA\Schema\Ref(ref: Article::class, qualifiers: ['of' => 'list<\\' . Article::class . '>']))->getRefKey();
        $plain = (new OA\Schema\Ref(ref: Article::class, qualifiers: ['of' => 'list<' . Article::class . '>']))->getRefKey();

        $this->assertInstanceOf(ComponentKey::class, $written);
        $this->assertSame('list<' . Article::class . '>', $written->qualifiers['of']);
        $this->assertEquals($plain, $written);
    }

    /**
     * A refined type is spelled as a property typed with it spells it, so both find one copy.
     */
    public function testARefKeepsARefinedTypeArgument(): void
    {
        $key = (new OA\Schema\Ref(ref: Article::class, qualifiers: ['of' => 'int<0,100>']))->getRefKey();

        $this->assertInstanceOf(ComponentKey::class, $key);
        $this->assertSame('int<0, 100>', $key->qualifiers['of']);
    }

    public function testARefKeepsTypeArgumentsTypeInfoCannotRead(): void
    {
        $key = (new OA\Schema\Ref(ref: Article::class, qualifiers: ['of' => 'Unknown']))->getRefKey();

        $this->assertInstanceOf(ComponentKey::class, $key);
        $this->assertSame('Unknown', $key->qualifiers['of']);
    }

    public function testARefWithQualifiersHasAKey(): void
    {
        $ref = new OA\Schema\Ref(ref: Article::class, qualifiers: ['groups' => ['light']]);

        $this->assertSame(Article::class . '{"groups":["light"]}', (string) $ref->getRefKey());
        $this->assertNull((new OA\Schema\Ref(ref: '#/components/schemas/Article'))->getRefKey());
    }
}
