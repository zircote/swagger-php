<?php
namespace SwaggerFixures;

use Exception;
use Swagger\Logger as SwgLogger;
use \Swagger\Logger;
use Swagger\Annotations as SWG;

/**
 * @SWG\Info(title="Fixture for ClassPropertiesTest", version="test")
 * @SWG\Definition()
 */
class Customer
{
    
    /**
     * The first name of the customer.
     *
     * @var string
     * @SWG\Property()
     */
    public $firstname;

    /**
     * @var null|string The second name of the customer.
     * @SWG\Property()
     */
    public $secondname;

    /**
     * The third name of the customer.
     *
     * @var string|null
     * @example Peter
     * @SWG\Property()
     */
    public $thirdname;

    /**
     * The unknown name of the customer.
     *
     * @var unknown|null
     * @SWG\Property()
     */
    public $fourthname;

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
     * @var Customer[]|null
     */
    public $friends;

    /**
     * for ContextTest
     */
    public function testResolvingFullyQualifiedNames()
    {
        $test = new SwgLogger();
        $test2 = new Logger();
        $test3 = new SWG\Contact();
        throw new Exception();
    }
}
