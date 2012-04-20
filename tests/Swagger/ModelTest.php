<?php

require_once 'Swagger\Model.php';

require_once 'PHPUnit\Framework\TestCase.php';

/**
 * Swagger_Model test case.
 */
class Swagger_ModelTest extends PHPUnit_Framework_TestCase
{

    /**
     *
     * @var Swagger_Model
     */
    private $Swagger_Model;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();
        
        // TODO Auto-generated Swagger_ModelTest::setUp()
        
        $this->Swagger_Model = new Swagger_Model(/* parameters */);
    
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        // TODO Auto-generated Swagger_ModelTest::tearDown()
        
        $this->Swagger_Model = null;
        
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
     * Tests Swagger_Model->__construct()
     */
    public function test__construct ()
    {
        // TODO Auto-generated Swagger_ModelTest->test__construct()
        $this->markTestIncomplete("__construct test not implemented");
        
        $this->Swagger_Model->__construct(/* parameters */);
    
    }

}

