<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

/**
 * The JSON Schema type names a schema's `type` can take.
 *
 * Every attribute that takes a `type` stores the string value, so `Type::String` and
 * `'string'` produce the same document.
 */
enum Type: string
{
    case String = 'string';
    case Number = 'number';
    case Integer = 'integer';
    case Boolean = 'boolean';
    case Array = 'array';
    case Object = 'object';
    case Null = 'null';
}
