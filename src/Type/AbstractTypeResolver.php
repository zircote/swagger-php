<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Type;

use OpenApi\Analysis;
use OpenApi\Annotations\AbstractAnnotation;
use OpenApi\Annotations as OA;
use OpenApi\TypeResolverInterface;
use OpenApi\Undefined;
use OpenApi\Utils\TypeMapper;

abstract class AbstractTypeResolver implements TypeResolverInterface
{
    protected TypeMapper $typeMapper;

    public function __construct()
    {
        $this->typeMapper = new TypeMapper();
    }

    protected function type2ref(OA\Schema $schema, Analysis $analysis, string $sourceClass = OA\Schema::class): void
    {
        if (!Undefined::isDefault($schema->type) && !is_array($schema->type)) {
            if (($typeSchema = $analysis->getAnnotationForSource($schema->type, $sourceClass)) instanceof AbstractAnnotation) {
                $schema->type = Undefined::UNDEFINED;
                $schema->ref = OA\Components::ref($typeSchema);
            }
        }
    }

    /**
     * @param string|array $type
     */
    public function mapNativeType(OA\Schema $schema, $type): bool
    {
        if (is_array($type)) {
            $schema->type = $this->typeMapper->toSpecTypes(
                array_map(static fn ($t): string => strtolower((string) $t), $type)
            );

            return true;
        }

        $result = $this->typeMapper->map($type);
        if (null === $result) {
            return false;
        }

        if ('mixed' === $result['type']) {
            return true;
        }

        if (null !== $result['format'] && Undefined::isDefault($schema->format)) {
            $schema->format = $result['format'];
        }

        $schema->type = $result['type'];

        return true;
    }

    public function native2spec(string $type): string
    {
        return $this->typeMapper->toSpecType($type);
    }

    public function augmentSchemaType(Analysis $analysis, OA\Schema $schema, string $sourceClass = OA\Schema::class): void
    {
        $context = $schema->_context;

        if (null === $context->reflector || $context->nested) {
            return;
        }

        /* @phpstan-ignore argument.type */
        $this->doAugment($analysis, $schema, $context->reflector, $sourceClass);

        $this->mapNativeType($schema, $schema->type);
    }

    /**
     * @param \ReflectionParameter|\ReflectionProperty|\ReflectionMethod $reflector
     */
    abstract protected function doAugment(Analysis $analysis, OA\Schema $schema, \Reflector $reflector, string $sourceClass = OA\Schema::class): void;
}
