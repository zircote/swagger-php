<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

use OpenApi\Annotations as OA;

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

    public function mapNativeType(OA\Schema $schema, $type): bool;

    public function native2spec(string $type): string;

    public function augmentSchemaType(Analysis $analysis, OA\Schema $schema): void;
}
