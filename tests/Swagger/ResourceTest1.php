<?php

require_once 'Swagger\Resource.php';

require_once 'PHPUnit\Framework\TestCase.php';

/**
 * Swagger_Resource test case.
 */
class Swagger_ResourceTest1 extends PHPUnit_Framework_TestCase
{

    /**
     *
     * @var Swagger_Resource
     */
    private $Swagger_Resource;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();
        
        // TODO Auto-generated Swagger_ResourceTest1::setUp()
        
        $this->Swagger_Resource = new Swagger_Resource(/* parameters */);
    
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        // TODO Auto-generated Swagger_ResourceTest1::tearDown()
        
        $this->Swagger_Resource = null;
        
        parent::tearDown();
    }

    /**
     * Constructs the test case.
     */
    public function __construct ()
    {
        // TODO Auto-generated constructor
    }

    /**
     * Tests Swagger_Resource->__construct()
     */
    public function test__construct ()
    {
        // TODO Auto-generated Swagger_ResourceTest1->test__construct()
        $this->markTestIncomplete("__construct test not implemented");
        
        $this->Swagger_Resource->__construct(/* parameters */);
    
    }

    /**
     * Tests Swagger_Resource->getResources()
     */
    public function testGetResources ()
    {
        // TODO Auto-generated Swagger_ResourceTest1->testGetResources()
        $this->markTestIncomplete("getResources test not implemented");
        
        $this->Swagger_Resource->getResources(/* parameters */);
    
    }

    /**
     * Tests Swagger_Resource->getResource()
     */
    public function testGetResource ()
    {
        // TODO Auto-generated Swagger_ResourceTest1->testGetResource()
        $this->markTestIncomplete("getResource test not implemented");
        
        $this->Swagger_Resource->getResource(/* parameters */);
    
    }

}

