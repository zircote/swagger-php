<?php

require_once 'Swagger.php';

require_once 'PHPUnit\Framework\TestCase.php';

/**
 * Swagger test case.
 */
class SwaggerTest extends PHPUnit_Framework_TestCase
{

    /**
     *
     * @var Swagger
     */
    private $Swagger;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();
        
        // TODO Auto-generated SwaggerTest::setUp()
        
        $this->Swagger = new Swagger(/* parameters */);
    
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        // TODO Auto-generated SwaggerTest::tearDown()
        
        $this->Swagger = null;
        
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
     * Tests Swagger::discover()
     */
    public function testDiscover ()
    {
        // TODO Auto-generated SwaggerTest::testDiscover()
        $this->markTestIncomplete("discover test not implemented");
        
        Swagger::discover(/* parameters */);
    
    }

    /**
     * Tests Swagger->getResourceNames()
     */
    public function testGetResourceNames ()
    {
        // TODO Auto-generated SwaggerTest->testGetResourceNames()
        $this->markTestIncomplete("getResourceNames test not implemented");
        
        $this->Swagger->getResourceNames(/* parameters */);
    
    }

    /**
     * Tests Swagger->getResource()
     */
    public function testGetResource ()
    {
        // TODO Auto-generated SwaggerTest->testGetResource()
        $this->markTestIncomplete("getResource test not implemented");
        
        $this->Swagger->getResource(/* parameters */);
    
    }

    /**
     * Tests Swagger->getApi()
     */
    public function testGetApi ()
    {
        // TODO Auto-generated SwaggerTest->testGetApi()
        $this->markTestIncomplete("getApi test not implemented");
        
        $this->Swagger->getApi(/* parameters */);
    
    }

    /**
     * Tests Swagger->__construct()
     */
    public function test__construct ()
    {
        // TODO Auto-generated SwaggerTest->test__construct()
        $this->markTestIncomplete("__construct test not implemented");
        
        $this->Swagger->__construct(/* parameters */);
    
    }

    /**
     * Tests Swagger->getClassList()
     */
    public function testGetClassList ()
    {
        // TODO Auto-generated SwaggerTest->testGetClassList()
        $this->markTestIncomplete("getClassList test not implemented");
        
        $this->Swagger->getClassList(/* parameters */);
    
    }

    /**
     * Tests Swagger->setClassList()
     */
    public function testSetClassList ()
    {
        // TODO Auto-generated SwaggerTest->testSetClassList()
        $this->markTestIncomplete("setClassList test not implemented");
        
        $this->Swagger->setClassList(/* parameters */);
    
    }

    /**
     * Tests Swagger->getFileList()
     */
    public function testGetFileList ()
    {
        // TODO Auto-generated SwaggerTest->testGetFileList()
        $this->markTestIncomplete("getFileList test not implemented");
        
        $this->Swagger->getFileList(/* parameters */);
    
    }

    /**
     * Tests Swagger->setFileList()
     */
    public function testSetFileList ()
    {
        // TODO Auto-generated SwaggerTest->testSetFileList()
        $this->markTestIncomplete("setFileList test not implemented");
        
        $this->Swagger->setFileList(/* parameters */);
    
    }

    /**
     * Tests Swagger->setResources()
     */
    public function testSetResources ()
    {
        // TODO Auto-generated SwaggerTest->testSetResources()
        $this->markTestIncomplete("setResources test not implemented");
        
        $this->Swagger->setResources(/* parameters */);
    
    }

    /**
     * Tests Swagger->setModels()
     */
    public function testSetModels ()
    {
        // TODO Auto-generated SwaggerTest->testSetModels()
        $this->markTestIncomplete("setModels test not implemented");
        
        $this->Swagger->setModels(/* parameters */);
    
    }

}

