<?php

require_once 'Swagger\Models.php';

require_once 'PHPUnit\Framework\TestCase.php';

/**
 * Swagger_Models test case.
 */
class Swagger_ModelsTest extends PHPUnit_Framework_TestCase
{

    /**
     *
     * @var Swagger_Models
     */
    private $Swagger_Models;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();
        
        // TODO Auto-generated Swagger_ModelsTest::setUp()
        
        $this->Swagger_Models = new Swagger_Models(/* parameters */);
    
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        // TODO Auto-generated Swagger_ModelsTest::tearDown()
        
        $this->Swagger_Models = null;
        
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
     * Tests Swagger_Models->__construct()
     */
    public function test__construct ()
    {
        // TODO Auto-generated Swagger_ModelsTest->test__construct()
        $this->markTestIncomplete("__construct test not implemented");
        
        $this->Swagger_Models->__construct(/* parameters */);
    
    }

}

