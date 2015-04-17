<?php
namespace SwaggerFixures;

use Exception;
use Swagger\Parser as SwgParser;
use \Swagger\Parser;
use Swagger\Annotations as SWG;

/**
 * @SWG\Info(title="Fixture for ClassPropertiesTest", version="test")
 * @SWG\Definition()
 */
class Customer {
    
    /**
     * The firstname of the customer.
     * @var string
     * @SWG\Property()
     */
    public $firstname;
    
    /**
     * @var string The lastname of the customer.
     * @SWG\Property()
     */
    public $lastname;
    
    /**
     * @SWG\Property()
     * @var string[]
     */
    public $tags;
    
    /**
     * @SWG\Property()
     * @var Customer
     */
    public $submittedBy;
    
    /**
     * @SWG\Property()
     * @var Customer[]
     */
    public $friends;

    /**
     * for ContextTest
     */
    function testResolvingFullyQualifiedNames() {
        $test = new SwgParser();
        $test2 = new Parser();
        $test3 = new SWG\Contact();
        throw new Exception();
    }
}
