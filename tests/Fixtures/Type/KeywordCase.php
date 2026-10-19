<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Type;

/**
 * Docblock keywords in other than lower case.
 */
final class KeywordCase
{
    /** @var String */
    public $string;

    /** @var Integer */
    public $integer;

    /** @var Boolean */
    public $boolean;

    /** @var Double */
    public $double;

    /** @var Array<String> */
    public $stringArray;

    /** @var ?Int */
    public $nullableInt;

    /** @var Number */
    public $number;

    /** @var NoSuchClass */
    public $unknown;
}
