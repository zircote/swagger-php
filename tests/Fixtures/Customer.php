<?php declare(strict_types=1);
namespace SwaggerFixures;

use Exception;
use Swagger\Logger as SwaggerLogger;
use \Swagger\Logger;
use Swagger\Annotations as OAS;

/**
 * @OAS\Info(title="Fixture for ClassPropertiesTest", version="test")
 * @OAS\Schema()
 */
class Customer
{

    /**
     * The firstname of the customer.
     * @var string
     * @OAS\Property()
     */
    public $firstname;

    /**
     * @var string The lastname of the customer.
     * @OAS\Property()
     */
    public $lastname;

    /**
     * @OAS\Property()
     * @var string[]
     */
    public $tags;

    /**
     * @OAS\Property()
     * @var Customer
     */
    public $submittedBy;

    /**
     * @OAS\Property()
     * @var Customer[]
     */
    public $friends;

    /**
     * for ContextTest
     */
    public function testResolvingFullyQualifiedNames()
    {
        $test = new SwaggerLogger();
        $test2 = new Logger();
        $test3 = new OAS\Contact();
        throw new Exception();
    }
}
