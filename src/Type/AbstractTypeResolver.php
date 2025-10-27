<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Type;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Generator;
use OpenApi\Processors\Concerns\TypesTrait;
use OpenApi\TypeResolverInterface;

abstract class AbstractTypeResolver implements TypeResolverInterface
{
    // todo: move
    use TypesTrait;

    protected function type2ref(OA\Schema $schema, Analysis $analysis): void
    {
        if (!Generator::isDefault($schema->type)) {
            if ($typeSchema = $analysis->getSchemaForSource($schema->type)) {
                $schema->type = Generator::UNDEFINED;
                $schema->ref = OA\Components::ref($typeSchema);
            }
        }
    }

    public function augmentSchemaType(Analysis $analysis, OA\Schema $schema): void
    {
        $context = $schema->_context;

        if (null === $context->reflector || $context->is('nested')) {
            return;
        }

        /* @phpstan-ignore argument.type */
        $this->doAugment($analysis, $schema, $context->reflector);

        $this->mapNativeType($schema, $schema->type);
    }

    /**
     * @param \ReflectionParameter|\ReflectionProperty|\ReflectionMethod $reflector
     */
    abstract protected function doAugment(Analysis $analysis, OA\Schema $schema, \Reflector $reflector): void;
}
