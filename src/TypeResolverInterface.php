<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

use OpenApi\Annotations as OA;
use OpenApi\Utils\TypeMapper;

interface TypeResolverInterface
{
    /** @deprecated Use TypeMapper::NATIVE_TYPE_MAP instead */
    public const NATIVE_TYPE_MAP = TypeMapper::NATIVE_TYPE_MAP;

    public function mapNativeType(OA\Schema $schema, $type): bool;

    public function native2spec(string $type): string;

    /**
     * @param class-string<OA\AbstractAnnotation> $sourceClass optional source class type hint for resolving references to
     *                                                         other types as `OA\Schema`
     */
    public function augmentSchemaType(Analysis $analysis, OA\Schema $schema, string $sourceClass = OA\Schema::class): void;
}
