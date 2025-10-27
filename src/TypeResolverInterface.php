<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

interface TypeResolverInterface
{
    public const NATIVE_TYPE_MAP = [
        'mixed' => 'mixed',
        'string' => 'string',
        'array' => 'array',
        'byte' => ['string', 'byte'],
        'boolean' => 'boolean',
        'bool' => 'boolean',
        'int' => 'integer',
        'integer' => 'integer',
        'long' => ['integer', 'long'],
        'float' => ['number', 'float'],
        'double' => ['number', 'double'],
        'date' => ['string', 'date'],
        'datetime' => ['string', 'date-time'],
        '\\datetime' => ['string', 'date-time'],
        'datetimeimmutable' => ['string', 'date-time'],
        '\\datetimeimmutable' => ['string', 'date-time'],
        'datetimeinterface' => ['string', 'date-time'],
        '\\datetimeinterface' => ['string', 'date-time'],
        'number' => 'number',
        'object' => 'object',
    ];

    /** @deprecated  */
    public function getReflectionTypeDetails(\Reflector $reflector): \stdClass;

    /** @deprecated  */
    public function getDocblockTypeDetails(\Reflector $reflector): \stdClass;
}
