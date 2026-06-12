<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Context;
use OpenApi\Generator;
use OpenApi\GeneratorAwareInterface;
use OpenApi\GeneratorAwareTrait;

/**
 * Use the RequestBody context to extract useful information and inject that into the annotation.
 */
class AugmentRequestBody implements GeneratorAwareInterface
{
    use GeneratorAwareTrait;

    public function __invoke(Analysis $analysis): void
    {
        $requestBodies = $analysis->getAnnotationsOfType(OA\RequestBody::class);

        $this->augmentRequestBody($analysis, $requestBodies);
    }

    /**
     * @param array<OA\RequestBody> $requestBodies
     */
    protected function augmentRequestBody(Analysis $analysis, array $requestBodies): void
    {
        foreach ($requestBodies as $requestBody) {
            if (!$requestBody->isRoot(OA\RequestBody::class)) {
                continue;
            }

            $context = $requestBody->_context;
            if (Generator::isDefault($requestBody->request)) {
                if ($context->is('class')) {
                    $requestBody->request = $requestBody->_context->class;
                } elseif ($context->is('interface')) {
                    $requestBody->request = $requestBody->_context->interface;
                } elseif ($context->is('trait')) {
                    $requestBody->request = $requestBody->_context->trait;
                } elseif ($context->is('enum')) {
                    $requestBody->request = $requestBody->_context->enum;
                }
            }

            if ($context->reflector instanceof \ReflectionParameter) {
                $schema = new OA\Schema(['_context' => new Context(['reflector' => $context->reflector], $context)]);
                $this->generator->getTypeResolver()->augmentSchemaType($analysis, $schema, OA\RequestBody::class);

                if (Generator::isDefault($requestBody->ref)) {
                    $requestBody->ref = $schema->ref;
                }

                if (Generator::isDefault($requestBody->required)) {
                    $requestBody->required = !$schema->isNullable();
                }
            }
        }
    }
}
