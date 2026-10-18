<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

/**
 * Where a nestable attribute goes when more than one attribute could hold it.
 *
 * An attribute without a `fan` behaves as `Single`.
 */
enum Fan
{
    /** Into exactly one: more than one match is an error that names them. */
    case Single;

    /** Into every match: the attribute itself into the first, a copy into each of the others. */
    case All;
}
