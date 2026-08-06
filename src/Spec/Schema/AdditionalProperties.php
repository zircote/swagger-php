<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec\Schema;

use OpenApi\Spec as OA;

/**
 * Typed alias for Schema used as the `additionalProperties` value.
 *
 * Identical to OA\Schema in functionality — exists for readability when declaring
 * schemas with constrained additional properties:
 *
 *     new OA\Schema(
 *         type: 'object',
 *         additionalProperties: new OA\Schema\AdditionalProperties(type: 'string'),
 *     )
 */
final class AdditionalProperties extends OA\Schema
{
}
