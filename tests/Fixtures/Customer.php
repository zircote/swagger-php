<?php declare(strict_types=1);

namespace OpenApiFixures;

use Exception;
use OpenApi\Annotations as OA;
use OpenApi\Logger;
use OpenApi\Logger as OpenApiLogger;

/**
 * @OA\Info(title="Fixture for ClassPropertiesTest", version="test")
 * @OA\Schema()
 */
class Customer
{

    /**
     * The firstname of the customer.
     *
     * @var string
     * @example test_user
     * @OA\Property()
     */
    public $firstname;

    /**
     * @var string The lastname of the customer.
     * @OA\Property()
     */
    public $lastname;

    /**
     * @OA\Property()
     * @var string[]
     */
    public $tags;

    /**
     * @OA\Property()
     * @var Customer
     */
    public $submittedBy;

    /**
     * @OA\Property()
     * @var Customer[]
     */
    public $friends;

    /**
     * for ContextTest
     */
    public function testResolvingFullyQualifiedNames()
    {
        $test = new OpenApiLogger();
        $test2 = new Logger();
        $test3 = new OA\Contact();
        throw new Exception();
    }
}
