<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Generator;
use OpenApi\Serializer;
use OpenApi\Tests\Concerns\GeneratesTestMatrix;
use OpenApi\Tests\Concerns\UsesExamples;
use OpenApi\Type\LegacyTypeResolver;
use OpenApi\TypeResolverInterface;
use PHPUnit\Framework\Attributes\DataProvider;

final class ExamplesTest extends OpenApiTestCase
{
    use GeneratesTestMatrix;
    use UsesExamples;

    private const EXAMPLES = [
        'api',
        'misc',
        'nesting',
        'petstore',
        'polymorphism',
        'using-interfaces',
        'using-links',
        'using-refs',
        'using-traits',
        'webhooks',
    ];

    private const IMPLEMENTATIONS = [
        'annotations',
        'attributes',
        'mixed',
        'hybrid',
        'spec',
    ];

    public static function exampleSpecs(): iterable
    {
        $resolvers = self::getTypeResolvers();

        foreach ($resolvers as $resolverName => $typeResolver) {
            foreach (self::EXAMPLES as $example) {
                foreach (self::IMPLEMENTATIONS as $implementation) {
                    if (!file_exists(self::examplePath($example) . '/' . $implementation)) {
                        continue;
                    }

                    foreach (self::matrixCombinations(
                        [
                            'version' => self::versions(),
                            'mode' => self::modes(),
                        ],
                        [
                            fn (array $c): bool => $implementation === 'spec' && in_array($c['mode'], [Mode::CLASSIC, Mode::HYBRID], true),
                            fn (array $c): bool => $implementation !== 'spec' && $c['mode'] === Mode::SPEC,
                            fn (array $c): bool => $implementation === 'hybrid' && $c['mode'] !== Mode::HYBRID,
                            fn (array $c): bool => $typeResolver instanceof LegacyTypeResolver && $c['mode'] !== Mode::CLASSIC,
                        ],
                    ) as $combo) {
                        $specFilename = self::getSpecFilename($example, $implementation, $combo['version'], $combo['mode']);
                        if (!file_exists($specFilename)) {
                            continue;
                        }

                        $key = self::matrixKey([$example, $resolverName, $implementation, $combo['mode']->value, $combo['version']]);

                        yield $key => [
                            $typeResolver,
                            $example,
                            $implementation,
                            $combo['version'],
                            $combo['mode'],
                        ];
                    }
                }
            }
        }
    }

    #[DataProvider('exampleSpecs')]
    public function testExample(TypeResolverInterface $typeResolver, string $name, string $implementation, string $version, Mode $mode): void
    {
        $this->registerExampleClassloader($name, $implementation);

        $this->ignoreLogEntries(
            'Schema: const is not supported in OpenAPI 3.0, using enum fallback',
            'License identifier is not supported in OpenAPI 3.0, use url instead',
        );

        $path = self::examplePath("{$name}/{$implementation}");
        $specFilename = self::getSpecFilename($name, $implementation, $version, $mode);

        $result = (new Builder())
            ->setMode($mode)
            ->addSource($path)
            ->setVersion($version)
            ->setLogger($this->getTrackingLogger())
            ->withGenerator(fn (Generator $generator): Generator => $generator->setTypeResolver($typeResolver))
            ->build();
        // file_put_contents($specFilename, $result->toYaml());
        $this->assertTrue($result->isValid());
        $this->assertSpecEquals(
            $result->toYaml(),
            file_get_contents($specFilename),
            "Example: {$name}/{$implementation}/" . basename($specFilename)
        );
    }

    #[DataProvider('exampleSpecs')]
    public function testSerializer(TypeResolverInterface $typeResolver, string $name, string $implementation, string $version, Mode $mode): void
    {
        if ($mode !== Mode::HYBRID) {
            return;
        }

        $specFilename = self::getSpecFilename($name, $implementation, $version);

        $reserialized = (new Serializer())->deserializeFile($specFilename)->toYaml();

        $this->assertSpecEquals(file_get_contents($specFilename), $reserialized);
    }
}
