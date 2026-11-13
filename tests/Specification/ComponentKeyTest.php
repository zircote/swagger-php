<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Specification;

use OpenApi\OpenApiException;
use OpenApi\Spec as OA;
use OpenApi\Specification\ComponentKey;
use OpenApi\Tests\Fixtures\ClassDescriptions\Article;
use OpenApi\Type\TypeArguments;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\TypeInfo\Type;

final class ComponentKeyTest extends TestCase
{
    public function testQualifiersAreSortedByName(): void
    {
        $key = new ComponentKey('App\Author', ['version' => 2, 'groups' => ['light']]);

        $this->assertSame(['groups', 'version'], array_keys($key->qualifiers));
        $this->assertTrue($key->equals(new ComponentKey('App\Author', ['groups' => ['light'], 'version' => 2])));
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

    public function testTypeArgumentsArePartOfTheKey(): void
    {
        $key = new ComponentKey('App\Envelope', typeArguments: new TypeArguments(Type::object('App\Customer')));

        $this->assertFalse($key->isPlain());
        $this->assertSame('App\Envelope<App\Customer>', (string) $key);
        $this->assertTrue($key->equals(new ComponentKey('App\Envelope', typeArguments: new TypeArguments(Type::object('\App\Customer')))));
        $this->assertFalse($key->equals(new ComponentKey('App\Envelope')));
        $this->assertFalse($key->equals(new ComponentKey('App\Envelope', typeArguments: new TypeArguments(Type::object('App\Order')))));
    }

    public function testTypeArgumentsKeepTheirOrder(): void
    {
        $key = new ComponentKey('App\Pair', typeArguments: new TypeArguments(Type::string(), Type::int()));

        $this->assertSame('App\Pair<string, int>', (string) $key);
        $this->assertFalse($key->equals(new ComponentKey('App\Pair', typeArguments: new TypeArguments(Type::int(), Type::string()))));
    }

    public function testTypeArgumentsComeBeforeTheQualifiers(): void
    {
        $key = new ComponentKey('App\Envelope', ['groups' => ['light']], new TypeArguments(Type::object('App\Customer')));

        $this->assertSame('App\Envelope<App\Customer>{"groups":["light"]}', (string) $key);
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

    public function testARefWithQualifiersHasAKey(): void
    {
        $ref = new OA\Schema\Ref(ref: Article::class, qualifiers: ['groups' => ['light']]);

        $this->assertSame(Article::class . '{"groups":["light"]}', (string) $ref->getRefKey());
        $this->assertNull((new OA\Schema\Ref(ref: '#/components/schemas/Article'))->getRefKey());
    }
}
