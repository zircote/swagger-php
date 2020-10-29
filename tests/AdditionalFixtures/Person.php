<?php declare(strict_types=1);

// NOTE: this file uses "\r\n" linebreaks on purpose

namespace OpenApi\Tests\AdditionalFixtures;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="Fixture for Inclusion Test", version="test")
 * @OA\Schema()
 */
class Person
{

    /**
     * The first name of the customer.
     *
     * @var string
     *
     * @example John
     * @OA\Property()
     */
    public $firstname;

    /**
     * @var null|string The second name of the customer.
     *
     * @example Allan
     * @OA\Property()
     */
    public $secondname;

    /**
     * The third name of the customer.
     *
     * @var null|string
     *
     * @example Peter
     * @OA\Property()
     */
    public $thirdname;

    /**
     * The unknown name of the customer.
     *
     * @var null|unknown
     *
     * @example Unknown
     * @OA\Property()
     */
    public $fourthname;

    /**
     * @var string The lastname of the customer.
     * @OA\Property()
     */
    public $lastname;
}
