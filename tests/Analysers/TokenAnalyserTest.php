<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Analysers;

use OpenApi\Analysis;
use OpenApi\Annotations\Info;
use OpenApi\Annotations\Property;
use OpenApi\Annotations\Schema;
use OpenApi\Generator;
use OpenApi\Analysers\TokenAnalyser;
use OpenApi\Tests\Fixtures\Parser\User;
use OpenApi\Tests\OpenApiTestCase;

class TokenAnalyserTest extends OpenApiTestCase
{
    protected function analysisFromCode(string $code): Analysis
    {
        $analyser = new TokenAnalyser();
        $analyser->setGenerator(new Generator());

        return $analyser->fromCode('<?php ' . $code, $this->getContext());
    }

    public function singleDefinitionCases()
    {
        return [
            'global-class' => ['class AClass {}', '\AClass', 'AClass', 'classes', 'class'],
            'global-interface' => ['interface AInterface {}', '\AInterface', 'AInterface', 'interfaces', 'interface'],
            'global-trait' => ['trait ATrait {}', '\ATrait', 'ATrait', 'traits', 'trait'],

            'namespaced-class' => ['namespace SNS\Foo; class AClass {}', '\SNS\Foo\AClass', 'AClass', 'classes', 'class'],
            'namespaced-interface' => ['namespace SNS\Foo; interface AInterface {}', '\SNS\Foo\AInterface', 'AInterface', 'interfaces', 'interface'],
            'namespaced-trait' => ['namespace SNS\Foo; trait ATrait {}', '\SNS\Foo\ATrait', 'ATrait', 'traits', 'trait'],
        ];
    }

    /**
     * @dataProvider singleDefinitionCases
     */
    public function testSingleDefinition($code, $fqdn, $name, $type, $typeKey)
    {
        $analysis = $this->analysisFromCode($code);

        $this->assertSame([$fqdn], array_keys($analysis->$type));
        $definition = $analysis->$type[$fqdn];
        $this->assertSame($name, $definition[$typeKey]);
        $this->assertTrue(!array_key_exists('extends', $definition) || !$definition['extends']);
        $this->assertSame([], $definition['properties']);
        $this->assertSame([], $definition['methods']);
    }

    public function extendsDefinitionCases()
    {
        return [
            'global-class' => ['class BClass extends Other {}', '\BClass', 'BClass', '\Other', 'classes', 'class'],
            'namespaced-class' => ['namespace NC\Foo; class BClass extends \Other {}', '\NC\Foo\BClass', 'BClass', '\Other', 'classes', 'class'],
            'global-class-explicit' => ['class EClass extends \Bar\Other {}', '\EClass', 'EClass', '\Bar\Other', 'classes', 'class'],
            'namespaced-class-explicit' => ['namespace NCE\Foo; class AClass extends \Bar\Other {}', '\NCE\Foo\AClass', 'AClass', '\Bar\Other', 'classes', 'class'],
            'global-class-use' => ['use XBar\Other; class XClass extends Other {}', '\XClass', 'XClass', '\XBar\Other', 'classes', 'class'],
            'namespaced-class-use' => ['namespace NCU\Foo; use YBar\Other; class AClass extends Other {}', '\NCU\Foo\AClass', 'AClass', '\YBar\Other', 'classes', 'class'],
            'namespaced-class-as' => ['namespace NCA\Foo; use Bar\Some as Other; class AClass extends Other {}', '\NCA\Foo\AClass', 'AClass', '\Bar\Some', 'classes', 'class'],
            'namespaced-class-same' => ['namespace NCS\Foo; class AClass extends Other {}', '\NCS\Foo\AClass', 'AClass', '\NCS\Foo\Other', 'classes', 'class'],

            'global-interface' => ['interface BInterface extends Other {}', '\BInterface', 'BInterface', ['\Other'], 'interfaces', 'interface'],
            'namespaced-interface' => ['namespace NI\Foo; interface AInterface extends \Other {}', '\NI\Foo\AInterface', 'AInterface', ['\Other'], 'interfaces', 'interface'],
            'global-interface-explicit' => ['interface XInterface extends \ZBar\Other {}', '\XInterface', 'XInterface', ['\ZBar\Other'], 'interfaces', 'interface'],
            'namespaced-interface-explicit' => ['namespace NIE\Foo; interface AInterface extends \ABar\Other {}', '\NIE\Foo\AInterface', 'AInterface', ['\ABar\Other'], 'interfaces', 'interface'],
            'global-interface-use' => ['use BBar\Other; interface YInterface extends Other {}', '\YInterface', 'YInterface', ['\BBar\Other'], 'interfaces', 'interface'],
            'namespaced-interface-use' => ['namespace NIU\Foo; use EBar\Other; interface AInterface extends Other {}', '\NIU\Foo\AInterface', 'AInterface', ['\EBar\Other'], 'interfaces', 'interface'],
            'namespaced-interface-use-multi' => ['namespace NIUM\Foo; use FBar\Other; interface AInterface extends Other, \More {}', '\NIUM\Foo\AInterface', 'AInterface', ['\FBar\Other', '\More'], 'interfaces', 'interface'],
            'namespaced-interface-as' => ['namespace NIA\Foo; use Bar\Some as Other; interface AInterface extends Other {}', '\NIA\Foo\AInterface', 'AInterface', ['\Bar\Some'], 'interfaces', 'interface'],
        ];
    }

    /**
     * @dataProvider extendsDefinitionCases
     */
    public function testExtendsDefinition($code, $fqdn, $name, $extends, $type, $typeKey)
    {
        $analysis = $this->analysisFromCode($code);

        $this->assertSame([$fqdn], array_keys($analysis->$type));
        $definition = $analysis->$type[$fqdn];
        $this->assertSame($name, $definition[$typeKey]);
        $this->assertSame($extends, $definition['extends']);
    }

    public function usesDefinitionCases()
    {
        return [
            'global-class-use' => ['class YClass { use Other; }', '\YClass', 'YClass', ['\Other'], 'classes', 'class'],
            'namespaced-class-use' => ['namespace UNCU\Foo; class AClass { use \Other; }', '\UNCU\Foo\AClass', 'AClass', ['\Other'], 'classes', 'class'],
            'namespaced-class-use-namespaced' => ['namespace UNCUN\Foo; use GBar\Other; class AClass { use Other; }', '\UNCUN\Foo\AClass', 'AClass', ['\GBar\Other'], 'classes', 'class'],
            'namespaced-class-use-namespaced-as' => ['namespace UNCUNA\Foo; use HBar\Other as Some; class AClass { use Some; }', '\UNCUNA\Foo\AClass', 'AClass', ['\HBar\Other'], 'classes', 'class'],

            'global-trait-use' => ['trait ATrait { use Other; }', '\ATrait', 'ATrait', ['\Other'], 'traits', 'trait'],
            'namespaced-trait-use' => ['namespace UNTU\Foo; trait ATrait { use \Other; }', '\UNTU\Foo\ATrait', 'ATrait', ['\Other'], 'traits', 'trait'],
            'namespaced-trait-use-explicit' => ['namespace UNTUE\Foo; trait ATrait { use \DBar\Other; }', '\UNTUE\Foo\ATrait', 'ATrait', ['\DBar\Other'], 'traits', 'trait'],
            'namespaced-trait-use-multi' => ['namespace UNTUEM\Foo; trait ATrait { use \Other; use \More; }', '\UNTUEM\Foo\ATrait', 'ATrait', ['\Other', '\More'], 'traits', 'trait'],
            'namespaced-trait-use-mixed' => ['namespace UNTUEX\Foo; use TBar\Other; trait ATrait { use Other, \More; }', '\UNTUEX\Foo\ATrait', 'ATrait', ['\TBar\Other', '\More'], 'traits', 'trait'],
            'namespaced-trait-use-as' => ['namespace UNTUEA\Foo; use MBar\Other as Some; trait ATrait { use Some; }', '\UNTUEA\Foo\ATrait', 'ATrait', ['\MBar\Other'], 'traits', 'trait'],
        ];
    }

    /**
     * @dataProvider usesDefinitionCases
     */
    public function testUsesDefinition($code, $fqdn, $name, $traits, $type, $typeKey)
    {
        $analysis = $this->analysisFromCode($code);

        $this->assertSame([$fqdn], array_keys($analysis->$type));
        $definition = $analysis->$type[$fqdn];
        $this->assertSame($name, $definition[$typeKey]);
        $this->assertSame($traits, $definition['traits']);
    }

    public function testWrongCommentType()
    {
        $analyser = new TokenAnalyser();
        $this->assertOpenApiLogEntryContains('Annotations are only parsed inside `/**` DocBlocks');
        $analyser->fromCode("<?php\n/*\n * @OA\Parameter() */", $this->getContext());
    }

    public function testThirdPartyAnnotations()
    {
        $generator = new Generator();
        $analyser = new TokenAnalyser();
        $analyser->setGenerator($generator);
        $defaultAnalysis = $analyser->fromFile($this->fixture('ThirdPartyAnnotations.php'), $this->getContext());
        $this->assertCount(3, $defaultAnalysis->annotations, 'Only read the @OA annotations, skip the others.');

        // Allow the analyser to parse 3rd party annotations, which might
        // contain useful info that could be extracted with a custom processor
        $generator->addNamespace('AnotherNamespace\\Annotations\\');
        $openapi = $generator
            ->setAnalyser(new TokenAnalyser())
            ->generate([$this->fixture('ThirdPartyAnnotations.php')]);
        $this->assertSame('api/3rd-party', $openapi->paths[0]->path);
        $this->assertCount(4, $openapi->_unmerged);

        $analysis = $openapi->_analysis;
        $annotations = $analysis->getAnnotationsOfType('AnotherNamespace\Annotations\Unrelated');
        $this->assertCount(4, $annotations);
        $context = $analysis->getContext($annotations[0]);
        $this->assertInstanceOf('OpenApi\Context', $context);
        $this->assertSame('ThirdPartyAnnotations', $context->class);
        $this->assertSame('\OpenApi\Tests\Fixtures\ThirdPartyAnnotations', $context->fullyQualifiedName($context->class));
        $this->assertCount(1, $context->annotations);
    }

    public function testAnonymousClassProducesNoError()
    {
        try {
            $analyser = new TokenAnalyser();
            $analysis = $analyser->fromFile($this->fixture('PHP/php7.php'), $this->getContext());
            $this->assertNotNull($analysis);
        } catch (\Throwable $t) {
            $this->fail("Analyser produced an error: {$t->getMessage()}");
        }
    }

    /**
     * dataprovider.
     */
    public function descriptions()
    {
        return [
            'class' => [
                ['classes', 'class'],
                'User',
                'Parser/User.php',
                '\OpenApi\Tests\Fixtures\Parser\User',
                '\OpenApi\Tests\Fixtures\Parser\Sub\SubClass',
                ['getFirstName'],
                null,
                ['\OpenApi\Tests\Fixtures\Parser\HelloTrait'], // use ... as ...
            ],
            'interface' => [
                ['interfaces', 'interface'],
                'UserInterface',
                'Parser/UserInterface.php',
                '\OpenApi\Tests\Fixtures\Parser\UserInterface',
                ['\OpenApi\Tests\Fixtures\Parser\OtherInterface'],
                null,
                null,
                null,
            ],
            'trait' => [
                ['traits', 'trait'],
                'HelloTrait',
                'Parser/HelloTrait.php',
                '\OpenApi\Tests\Fixtures\Parser\HelloTrait',
                null,
                null,
                null,
                ['\OpenApi\Tests\Fixtures\Parser\OtherTrait', '\OpenApi\Tests\Fixtures\Parser\AsTrait'],
            ],
        ];
    }

    /**
     * @dataProvider descriptions
     */
    public function testDescription($type, $name, $fixture, $fqdn, $extends, $methods, $interfaces, $traits)
    {
        $analysis = $this->analysisFromFixtures([$fixture]);

        list($pType, $sType) = $type;
        $description = $analysis->$pType[$fqdn];

        $this->assertSame($name, $description[$sType]);
        if (null !== $extends) {
            $this->assertSame($extends, $description['extends']);
        }
        if (null !== $methods) {
            $this->assertSame($methods, array_keys($description['methods']));
        }
        if (null !== $interfaces) {
            $this->assertSame($interfaces, $description['interfaces']);
        }
        if (null !== $traits) {
            $this->assertSame($traits, $description['traits']);
        }
    }

    public function testNamespacedConstAccess()
    {
        $analysis = $this->analysisFromFixtures(['Parser/User.php']);
        $schemas = $analysis->getAnnotationsOfType(Schema::class, true);

        $this->assertCount(1, $schemas);
        $this->assertEquals(User::CONSTANT, $schemas[0]->example);
    }

    /**
     * @requires PHP 8
     */
    public function testPhp8AttributeMix()
    {
        $analysis = $this->analysisFromFixtures(['PHP/Label.php', 'PHP/Php8AttrMix.php']);
        $schemas = $analysis->getAnnotationsOfType(Schema::class, true);

        $this->assertCount(1, $schemas);
        $analysis->process((new Generator())->getProcessors());

        $properties = $analysis->getAnnotationsOfType(Property::class, true);
        $this->assertCount(2, $properties);
        $this->assertEquals('id', $properties[0]->property);
        $this->assertEquals('otherId', $properties[1]->property);
    }

    /**
     * @requires PHP 8
     */
    public function testPhp8NamedProperty()
    {
        $analysis = $this->analysisFromFixtures(['PHP/Php8NamedProperty.php'], [], new TokenAnalyser());
        $schemas = $analysis->getAnnotationsOfType(Schema::class, true);

        $this->assertCount(1, $schemas);
        $analysis->process((new Generator())->getProcessors());

        $properties = $analysis->getAnnotationsOfType(Property::class, true);
        $this->assertCount(1, $properties);
        $this->assertEquals('labels', $properties[0]->property);
    }

    public function testAnonymousFunctions()
    {
        $analysis = $this->analysisFromFixtures(['PHP/AnonymousFunctions.php'], [], new TokenAnalyser());
        $analysis->process((new Generator())->getProcessors());

        $infos = $analysis->getAnnotationsOfType(Info::class, true);
        $this->assertCount(1, $infos);
    }

    /**
     * @requires PHP 8
     */
    public function testPhp8NamedArguments()
    {
        $analysis = $this->analysisFromFixtures(['PHP/Php8NamedArguments.php'], [], new TokenAnalyser());
        $schemas = $analysis->getAnnotationsOfType(Schema::class, true);

        $this->assertCount(1, $schemas);
        $analysis->process((new Generator())->getProcessors());
    }
}
