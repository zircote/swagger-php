<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Spec;

use OpenApi\Spec as OA;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AuthoredTest extends TestCase
{
    public function testAFieldPassedToTheConstructorIsAuthored(): void
    {
        $schema = new OA\Schema(description: 'A pet');

        $this->assertTrue($schema->isAuthored('description'));
        $this->assertFalse($schema->isAuthored('title'));
    }

    public function testAFieldSetAfterConstructionIsNotAuthored(): void
    {
        $schema = new OA\Schema();
        $schema->description = 'From a docblock';

        $this->assertFalse($schema->isAuthored('description'));
    }

    public function testACopyKeepsWhatWasAuthored(): void
    {
        $copy = (new OA\Schema(description: 'A pet'))->copy();

        $this->assertTrue($copy->isAuthored('description'));
    }

    /**
     * @return iterable<string, array{class-string<OA\AbstractAttribute>}>
     */
    public static function attributeClassesProvider(): iterable
    {
        /** @var \SplFileInfo $file */
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(__DIR__ . '/../../src/Spec')) as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $relative = substr($file->getPathname(), strlen(__DIR__ . '/../../src/Spec/'), -4);
            $class = 'OpenApi\\Spec\\' . str_replace('/', '\\', $relative);
            if (!is_subclass_of($class, OA\AbstractAttribute::class)) {
                continue;
            }

            $reflector = new \ReflectionClass($class);
            if (!$reflector->isAbstract()) {
                yield $reflector->getShortName() . ' (' . $relative . ')' => [$class];
            }
        }
    }

    /**
     * A constructor that assigns a field after calling its parent's leaves that field out of the
     * snapshot, so every argument given is checked against `isAuthored()`.
     *
     * @param class-string<OA\AbstractAttribute> $class
     */
    #[DataProvider('attributeClassesProvider')]
    public function testEveryConstructorArgumentIsAuthored(string $class): void
    {
        $reflector = new \ReflectionClass($class);
        $arguments = [];
        foreach ($reflector->getConstructor()?->getParameters() ?? [] as $parameter) {
            $arguments[$parameter->getName()] = match ($parameter->getName()) {
                'properties' => ['name' => new OA\Schema()],
                default => $this->valueFor($parameter->getType()),
            };
        }

        $attribute = $reflector->newInstanceArgs($arguments);

        $missing = [];
        foreach (array_keys($arguments) as $name) {
            if ($reflector->hasProperty($name) && $reflector->getProperty($name)->isPublic() && !$attribute->isAuthored($name)) {
                $missing[] = $name;
            }
        }

        $this->assertSame([], $missing, "{$class} assigns these after its parent constructor has run");
    }

    /**
     * A value of the simplest type the parameter accepts.
     */
    protected function valueFor(?\ReflectionType $type): mixed
    {
        $names = $type instanceof \ReflectionUnionType
            ? array_map(fn (\ReflectionType $member): string => (string) $member, $type->getTypes())
            : [ltrim((string) $type, '?')];

        foreach (['string' => 'value', 'mixed' => 'value', 'int' => 1, 'float' => 1.5, 'bool' => true, 'array' => ['value']] as $name => $value) {
            if (in_array($name, $names, true)) {
                return $value;
            }
        }

        foreach ($names as $name) {
            if (enum_exists($name)) {
                return $name::cases()[0];
            }

            if (class_exists($name)) {
                return (new \ReflectionClass($name))->newInstance();
            }
        }

        return null;
    }
}
