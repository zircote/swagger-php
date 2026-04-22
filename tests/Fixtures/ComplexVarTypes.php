<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

use OpenApi\Attributes as OAT;

#[OAT\Schema]
class ComplexVarTypes
{
    /**
     * An associative array with string values.
     *
     * @var array<string, string>
     */
    #[OAT\Property]
    public array $map;

    /**
     * A map from int to user objects.
     *
     * @var array<int, User>
     */
    #[OAT\Property]
    public array $userMap;

    /** @var array<string, string> Inline generic with description */
    #[OAT\Property]
    public array $inlineGenericDesc;

    /**
     * A map using namespaced class.
     *
     * @var array<int, Customer>
     */
    #[OAT\Property]
    public array $namespacedMap;

    /**
     * List of integer IDs.
     *
     * @var int[]
     */
    #[OAT\Property]
    public array $intList;

    /**
     * Either an array or a string list.
     *
     * @var array|string[]
     */
    #[OAT\Property]
    public $arrayOrStringList;

    /**
     * Nullable map of strings.
     *
     * @var array<string, string>|null
     */
    #[OAT\Property]
    public ?array $nullableMap;

    /**
     * A collection of users or a single user array.
     *
     * @var array<int, User>|User[]
     */
    #[OAT\Property]
    public array $mixedUserList;

    /** @var array<string, string>|null Nullable inline with description */
    #[OAT\Property]
    public ?array $nullableInlineDesc;
}
