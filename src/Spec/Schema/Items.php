<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec\Schema;

use OpenApi\Spec as OA;

/**
 * Shortcut for `OA\Schema` with type `array` and `items`.
 *
 * Allows to shorten this:
 *
 *   #[OA\Schema]
 *   class Pet {
 *       #[OA\Property]
 *       #[OA\Schema(type: 'array', items: new OA\Schema(ref: MyModel::class))]
 *       public array $names;
 *   }
 *
 * to this:
 *
 *   #[OA\Schema]
 *   class Pet {
 *       #[OA\Property]
 *       #[OA\Schema\Items(ref: MyModel::class)]
 *       public array $names;
 *   }
 *
 * The `Shortcuts` augmenter wraps this into `OA\Schema(type: 'array', items: ...)` automatically.
 *
 * @see [Schema Object](https://spec.openapis.org/oas/v3.1.1.html#schema-object)
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER | \Attribute::IS_REPEATABLE)]
class Items extends OA\Schema
{
}
