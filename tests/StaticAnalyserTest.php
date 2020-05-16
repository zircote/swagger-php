<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApiTests;

use OpenApi\Analyser;
use OpenApi\Annotations\Property;
use OpenApi\Context;
use OpenApi\StaticAnalyser;

class StaticAnalyserTest extends OpenApiTestCase
{
    public function testWrongCommentType()
    {
        $analyser = new StaticAnalyser();
        $this->assertOpenApiLogEntryStartsWith('Annotations are only parsed inside `/**` DocBlocks');
        $analyser->fromCode("<?php\n/*\n * @OA\Parameter() */", new Context());
    }

    public function testIndentationCorrection()
    {
        $analysis = $this->analysisFromFixtures('StaticAnalyser/routes.php');
        $this->assertCount(20, $analysis->annotations);
    }

    public function testTrait()
    {
        $analysis = $this->analysisFromFixtures('Parser/HelloTrait.php');
        $this->assertCount(2, $analysis->annotations);
        $property = $analysis->getAnnotationsOfType(Property::class);
        $this->assertSame('HelloTrait', $property[0]->_context->trait);
    }

    public function testThirdPartyAnnotations()
    {
        $backup = Analyser::$whitelist;
        Analyser::$whitelist = ['OpenApi\Annotations\\'];
        $analyser = new StaticAnalyser();
        $defaultAnalysis = $analyser->fromFile(__DIR__.'/Fixtures/ThirdPartyAnnotations.php');
        $this->assertCount(3, $defaultAnalysis->annotations, 'Only read the @OA annotations, skip the others.');

        // Allow the analyser to parse 3rd party annotations, which might
        // contain useful info that could be extracted with a custom processor
        Analyser::$whitelist[] = 'Zend\Form\Annotation';
        $openapi = \OpenApi\scan(__DIR__.'/Fixtures/ThirdPartyAnnotations.php');
        $this->assertSame('api/3rd-party', $openapi->paths[0]->path);
        $this->assertCount(10, $openapi->_unmerged);
        Analyser::$whitelist = $backup;
        $analysis = $openapi->_analysis;
        $annotations = $analysis->getAnnotationsOfType('Zend\Form\Annotation\Name');
        $this->assertCount(1, $annotations);
        $context = $analysis->getContext($annotations[0]);
        $this->assertInstanceOf('OpenApi\Context', $context);
        $this->assertSame('ThirdPartyAnnotations', $context->class);
        $this->assertSame('\OpenApiFixtures\ThirdPartyAnnotations', $context->fullyQualifiedName($context->class));
        $this->assertCount(2, $context->annotations);
    }

    public function testAnonymousClassProducesNoError()
    {
        try {
            $analyser = new StaticAnalyser($this->fixtures('StaticAnalyser/php7.php')[0]);
            $this->assertNotNull($analyser);
        } catch (\Throwable $t) {
            $this->fail("Analyser produced an error: {$t->getMessage()}");
        }
    }

    /**
     * dataprovider
     */
    public function descriptions()
    {
        return [
            'class' => [
                ['classes', 'class'],
                'User',
                'Parser/User.php',
                '\OpenApiTests\Fixtures\Parser\User',
                '\OpenApiTests\Fixtures\Parser\Sub\SubClass',
                ['getFirstName'],
                null,
                ['Hello'], // use ... as ...
            ],
            'interface' => [
                ['interfaces', 'interface'],
                'UserInterface',
                'Parser/UserInterface.php',
                '\OpenApiTests\Fixtures\Parser\UserInterface',
                '\OpenApiTests\Fixtures\Parser\OtherInterface',
                null,
                null,
                null,
            ],
            'trait' => [
                ['traits', 'trait'],
                'HelloTrait',
                'Parser/HelloTrait.php',
                '\OpenApiTests\Fixtures\Parser\HelloTrait',
                null,
                null,
                null,
                ['OtherTrait'],
            ],
        ];
    }

    /**
     * @dataProvider descriptions
     */
    public function testDescription($type, $name, $fixture, $fqdn, $extends, $methods, $interfaces, $traits)
    {
        $analysis = $this->analysisFromFixtures($fixture);

        list ($pType, $sType) = $type;
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
}
