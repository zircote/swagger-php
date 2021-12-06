<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

class AnalysisTest extends OpenApiTestCase
{
    public function testGetSubclasses()
    {
        $analysis = $this->analysisFromFixtures([
            'AnotherNamespace/Child.php',
            'ExpandClasses/GrandAncestor.php',
            'ExpandClasses/Ancestor.php',
        ]);

        $this->assertCount(3, $analysis->classes, '3 classes should\'ve been detected');

        $subclasses = $analysis->getSubClasses('\\OpenApi\\Tests\\Fixtures\\ExpandClasses\\GrandAncestor');
        $this->assertCount(2, $subclasses, 'GrandAncestor has 2 subclasses');
        $this->assertSame(
            ['\\OpenApi\\Tests\\Fixtures\\ExpandClasses\\Ancestor', '\\AnotherNamespace\\Child'],
            array_keys($subclasses)
        );
        $this->assertSame(
            ['\AnotherNamespace\Child'],
            array_keys($analysis->getSubClasses('\\OpenApi\\Tests\\Fixtures\\ExpandClasses\\Ancestor'))
        );
    }

    public function testGetAllAncestorClasses()
    {
        $analysis = $this->analysisFromFixtures([
            'AnotherNamespace/Child.php',
            'ExpandClasses/GrandAncestor.php',
            'ExpandClasses/Ancestor.php',
        ]);

        $this->assertCount(3, $analysis->classes, '3 classes should\'ve been detected');

        $superclasses = $analysis->getSuperClasses('\AnotherNamespace\Child');
        $this->assertCount(2, $superclasses, 'Child has a chain of 2 super classes');
        $this->assertSame(
            ['\OpenApi\Tests\Fixtures\ExpandClasses\Ancestor', '\OpenApi\Tests\Fixtures\ExpandClasses\GrandAncestor'],
            array_keys($superclasses)
        );
        $this->assertSame(
            ['\OpenApi\Tests\Fixtures\ExpandClasses\GrandAncestor'],
            array_keys($analysis->getSuperClasses('\OpenApi\Tests\Fixtures\ExpandClasses\Ancestor'))
        );
    }

    public function testGetDirectAncestorClass()
    {
        $analysis = $this->analysisFromFixtures([
            'AnotherNamespace/Child.php',
            'ExpandClasses/GrandAncestor.php',
            'ExpandClasses/Ancestor.php',
        ]);

        $this->assertCount(3, $analysis->classes, '3 classes should\'ve been detected');

        $superclasses = $analysis->getSuperClasses('\AnotherNamespace\Child', true);
        $this->assertCount(1, $superclasses, 'Child has 1 parent class');
        $this->assertSame(
            ['\OpenApi\Tests\Fixtures\ExpandClasses\Ancestor'],
            array_keys($superclasses)
        );
        $this->assertSame(
            ['\OpenApi\Tests\Fixtures\ExpandClasses\GrandAncestor'],
            array_keys($analysis->getSuperClasses('\OpenApi\Tests\Fixtures\ExpandClasses\Ancestor', true))
        );
    }

    public function testGetInterfacesOfClass()
    {
        $analysis = $this->analysisFromFixtures([
            'Parser/User.php',
            'Parser/UserInterface.php',
            'Parser/OtherInterface.php',
        ]);

        $this->assertCount(1, $analysis->classes);
        $this->assertCount(2, $analysis->interfaces);

        $interfaces = $analysis->getInterfacesOfClass('\OpenApi\Tests\Fixtures\Parser\User');
        $this->assertCount(2, $interfaces);
        $this->assertSame([
            '\OpenApi\Tests\Fixtures\Parser\UserInterface',
            '\OpenApi\Tests\Fixtures\Parser\OtherInterface',
        ], array_keys($interfaces));
    }

    public function testGetTraitsOfClass()
    {
        $analysis = $this->analysisFromFixtures([
            'Parser/User.php',
            'Parser/HelloTrait.php',
            'Parser/OtherTrait.php',
            'Parser/AsTrait.php',
            'Parser/StaleTrait.php',
        ]);

        $this->assertCount(1, $analysis->classes);
        $this->assertCount(4, $analysis->traits);

        $traits = $analysis->getTraitsOfClass('\OpenApi\Tests\Fixtures\Parser\User');
        $this->assertSame([
            '\OpenApi\Tests\Fixtures\Parser\HelloTrait',
            '\OpenApi\Tests\Fixtures\Parser\OtherTrait',
            '\OpenApi\Tests\Fixtures\Parser\AsTrait',
        ], array_keys($traits));
    }
}
