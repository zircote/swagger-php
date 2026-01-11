<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

// NOTE: this file uses "\r\n" linebreaks on purpose

namespace OpenApi\Tests\Fixtures;

use Exception;
use OpenApi\Attributes as OAT;
use OpenApi\Generator;
use OpenApi\Generator as OpenApiGenerator;

/**
 * A customer.
 */
#[OAT\Info(title: 'Fixture for ClassPropertiesTest', version: 'test')]
#[OAT\Schema]
class Customer
{
    /**
     * The first name of the customer.
     *
     * @example John
     * @var string
     */
    #[OAT\Property]
    public $firstname;

    /**
     * @example Allan
     * @var null|string the second name of the customer
     */
    #[OAT\Property]
    public $secondname;

    /**
     * The third name of the customer.
     *
     * @example Peter
     * @var string|null
     */
    #[OAT\Property]
    public $thirdname;

    /**
     * The unknown name of the customer.
     *
     * @example Unknown
     * @var unknown|null
     */
    #[OAT\Property]
    public $fourthname;

    #[OAT\Property(nullable: false, format: 'number', example: '0.00')]
    public ?string $iq;

    /**
     * @var string the lastname of the customer
     */
    #[OAT\Property]
    public $lastname;

    /**
     * @var string[]
     */
    #[OAT\Property]
    public $tags;

    /**
     * @var Customer
     */
    #[OAT\Property]
    public $submittedBy;

    /**
     * @var Customer[]
     */
    #[OAT\Property]
    public $friends;

    /**
     * @var Customer|null
     */
    #[OAT\Property]
    public $bestFriend;

    /**
     * @var Customer[]|null
     */
    #[OAT\Property]
    public $endorsedFriends;

    /**
     * for ContextTest.
     */
    public function testResolvingFullyQualifiedNames(): void
    {
        (new OpenApiGenerator())->getLogger();
        (new Generator())->getLogger();
        new OAT\Contact();
        throw new Exception();
    }
}
