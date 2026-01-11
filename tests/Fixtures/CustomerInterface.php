<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'Customer',
    description: 'Fixture for Interface Test',
)]
interface CustomerInterface
{
    /**
     * The first name of the customer.
     *
     * @example John
     * @var string
     */
    #[OAT\Property]
    public function firstname();

    /**
     * @var null|string the second name of the customer
     *
     * @example Allan
     */
    #[OAT\Property]
    public function secondname();

    /**
     * The third name of the customer.
     *
     * @example Peter
     * @var string|null
     */
    #[OAT\Property]
    public function thirdname();

    /**
     * The unknown name of the customer.
     *
     * @example Unknown
     * @var unknown|null
     */
    #[OAT\Property]
    public function fourthname();

    /**
     * @var string the lastname of the customer
     */
    #[OAT\Property]
    public function lastname();

    /**
     * @var string[]
     */
    #[OAT\Property]
    public function tags();

    /**
     * @var Customer
     */
    #[OAT\Property]
    public function submittedBy();

    /**
     * @var Customer[]
     */
    #[OAT\Property]
    public function friends();

    /**
     * @var Customer|null
     */
    #[OAT\Property]
    public function bestFriend();
}
