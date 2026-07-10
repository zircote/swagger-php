<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Concerns;

use OpenApi\Spec as OA;
use OpenApi\Specification;
use Symfony\Component\Yaml\Yaml;

/**
 * Assertions for comparing schema structure (allOf refs + property names)
 * against an expected YAML fixture, independent of property order.
 */
trait AssertsSchemaStructure
{
    /**
     * Assert that compiled schemas (arrays) match the expected structure file.
     *
     * @param array<string, array> $schemas compiled schema arrays keyed by name
     */
    protected function assertCompiledSchemasMatchFile(array $schemas, string $expectedFile, string $context = ''): void
    {
        $expected = Yaml::parseFile($expectedFile);
        $prefix = $context !== '' ? "[{$context}] " : '';

        ksort($schemas);

        $this->assertSame(
            array_keys($expected),
            array_keys($schemas),
            $prefix . 'Schema names should match expected',
        );

        foreach ($expected as $name => $expectedSchema) {
            $actual = $schemas[$name];

            if (isset($expectedSchema['allOf'])) {
                $this->assertArrayHasKey('allOf', $actual, "{$prefix}Schema '{$name}' should have allOf");

                $actualRefs = array_values(array_filter(array_map(
                    fn (array $entry): ?string => $entry['$ref'] ?? null,
                    $actual['allOf'],
                )));
                $this->assertSame($expectedSchema['allOf'], $actualRefs, "{$prefix}Schema '{$name}' allOf refs");

                $actualProps = $this->extractAllOfPropertyNames($actual['allOf']);
            } else {
                $this->assertArrayNotHasKey('allOf', $actual, "{$prefix}Schema '{$name}' should not have allOf");
                $actualProps = array_keys($actual['properties'] ?? []);
            }

            $expectedProps = $expectedSchema['properties'];
            sort($expectedProps);
            sort($actualProps);
            $this->assertSame($expectedProps, $actualProps, "{$prefix}Schema '{$name}' properties");
        }
    }

    /**
     * Assert that Specification schemas (DTO objects) match the expected structure file.
     */
    protected function assertSpecificationSchemasMatchFile(Specification $specification, string $expectedFile, string $context = ''): void
    {
        $expected = Yaml::parseFile($expectedFile);
        $prefix = $context !== '' ? "[{$context}] " : '';

        foreach ($expected as $name => $expectedSchema) {
            $schema = $this->findSchemaByName($specification, $name);
            $this->assertInstanceOf(OA\Schema::class, $schema, "{$prefix}Schema '{$name}' should exist");

            if (isset($expectedSchema['allOf'])) {
                $this->assertNotNull($schema->allOf, "{$prefix}Schema '{$name}' should have allOf");

                $actualRefs = array_values(array_filter(
                    array_map(fn (OA\Schema $s): ?string => $s->ref, $schema->allOf),
                ));
                $this->assertSame($expectedSchema['allOf'], $actualRefs, "{$prefix}Schema '{$name}' allOf refs");

                $this->assertNull($schema->properties, "{$prefix}Schema '{$name}' properties should be in allOf");

                $actualProps = [];
                foreach ($schema->allOf as $entry) {
                    if ($entry->properties !== null) {
                        foreach ($entry->properties as $p) {
                            $actualProps[] = $p->property;
                        }
                    }
                }
            } else {
                $this->assertNull($schema->allOf, "{$prefix}Schema '{$name}' should not have allOf");
                $this->assertNotNull($schema->properties, "{$prefix}Schema '{$name}' should have properties");

                $actualProps = array_map(fn (OA\Property $p): string => $p->property, $schema->properties);
            }

            $expectedProps = $expectedSchema['properties'];
            sort($expectedProps);
            sort($actualProps);
            $this->assertSame($expectedProps, $actualProps, "{$prefix}Schema '{$name}' properties");
        }
    }

    protected function findSchemaByName(Specification $specification, string $name): ?OA\Schema
    {
        foreach ($specification->schemas as $schema) {
            if ($schema->schema === $name) {
                return $schema;
            }

            $reflector = $schema->getReflector();
            if ($reflector instanceof \ReflectionClass && $reflector->getShortName() === $name) {
                return $schema;
            }
        }

        return null;
    }

    /**
     * @return list<string>
     */
    protected function extractAllOfPropertyNames(array $allOf): array
    {
        $names = [];
        foreach ($allOf as $entry) {
            if (isset($entry['properties'])) {
                array_push($names, ...array_keys($entry['properties']));
            }
        }

        return $names;
    }
}
