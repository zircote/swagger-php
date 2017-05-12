<?php

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

use Swagger\Analysis;
use Swagger\StaticAnalyser;

class AnalysisTest extends SwaggerTestCase
{
    public function testRegisterProcessor()
    {
        $counter = 0;
        $analysis = new Analysis();
        $analysis->process();
        $this->assertSame(0, $counter);
        $countProcessor = function (Analysis $a) use (&$counter) {
            $counter++;
        };
        Analysis::registerProcessor($countProcessor);
        $analysis->process();
        $this->assertSame(1, $counter);
        Analysis::unregisterProcessor($countProcessor);
        $analysis->process();
        $this->assertSame(1, $counter);
    }
    
    public function testGetSubclasses()
    {
        $analyser = new StaticAnalyser();
        $analysis = $analyser->fromFile(__DIR__ . '/Fixtures/Child.php');
        $analysis->addAnalysis($analyser->fromFile(__DIR__ . '/Fixtures/GrandAncestor.php'));
        $analysis->addAnalysis($analyser->fromFile(__DIR__ . '/Fixtures/Ancestor.php'));
        
        $this->assertCount(3, $analysis->classes, '3 classes should\'ve been detected');
        
        $subclasses = $analysis->getSubClasses('\SwaggerFixtures\GrandAncestor');
        $this->assertCount(2, $subclasses, 'GrandAncestor has 2 subclasses');
        $this->assertSame(['\SwaggerFixtures\Ancestor', '\AnotherNamespace\Child'], array_keys($subclasses));
        $this->assertSame(['\AnotherNamespace\Child'], array_keys($analysis->getSubClasses('\SwaggerFixtures\Ancestor')));
    }
    
    public function testGetAncestorClasses()
    {
        $analyser = new StaticAnalyser();
        $analysis = $analyser->fromFile(__DIR__ . '/Fixtures/Child.php');
        $analysis->addAnalysis($analyser->fromFile(__DIR__ . '/Fixtures/GrandAncestor.php'));
        $analysis->addAnalysis($analyser->fromFile(__DIR__ . '/Fixtures/Ancestor.php'));
        
        $this->assertCount(3, $analysis->classes, '3 classes should\'ve been detected');
        
        $superclasses = $analysis->getSuperClasses('\AnotherNamespace\Child');
        $this->assertCount(2, $superclasses, 'Child has a chain of 2 super classes');
        $this->assertSame(['\SwaggerFixtures\Ancestor', '\SwaggerFixtures\GrandAncestor'], array_keys($superclasses));
        $this->assertSame(['\SwaggerFixtures\GrandAncestor'], array_keys($analysis->getSuperClasses('\SwaggerFixtures\Ancestor')));
    }
}
