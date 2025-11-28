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
use OpenApi\Processors\Concerns\DocblockTrait;

/**
 * Augments shared and operations parameters from docblock comments.
 */
class AugmentParameters implements GeneratorAwareInterface
{
    use DocblockTrait;

    use GeneratorAwareTrait;

    protected bool $augmentOperationParameters;

    public function __construct(bool $augmentOperationParameters = true)
    {
        $this->augmentOperationParameters = $augmentOperationParameters;
    }

    public function isAugmentOperationParameters(): bool
    {
        return $this->augmentOperationParameters;
    }

    /**
     * If set to <code>true</code> try to find operation parameter descriptions in the operation docblock.
     */
    public function setAugmentOperationParameters(bool $augmentOperationParameters): AugmentParameters
    {
        $this->augmentOperationParameters = $augmentOperationParameters;

        return $this;
    }

    public function __invoke(Analysis $analysis): void
    {
        $this->augmentParameters($analysis);
        $this->augmentSharedParameters($analysis);
        if ($this->augmentOperationParameters) {
            $this->augmentOperationParameters($analysis);
        }
    }

    protected function augmentParameters(Analysis $analysis): void
    {
        $parameters = $analysis->getAnnotationsOfType(OA\Parameter::class);

        foreach ($parameters as $parameter) {
            $context = $parameter->_context;

            if (Generator::isDefault($parameter->name) && null !== $context->reflector && method_exists($context->reflector, 'getName')) {
                $parameter->name = $context->reflector->getName();
            }

            if ($context->reflector instanceof \ReflectionParameter) {
                $schema = new OA\Schema([
                    '_context' => new Context([
                        'generated' => true,
                        'reflector' => $context->reflector,
                    ], $context),
                ]);
                $this->generator->getTypeResolver()->augmentSchemaType($analysis, $schema);

                $parameter->merge([new OA\Schema([
                    'type' => $schema->type,
                    'format' => $schema->format,
                    'ref' => $schema->ref,
                    '_context' => new Context([
                        'nested' => $this,
                        'comment' => null,
                        'reflector' => $context->reflector,
                    ], $context)]),
                ]);

                if (Generator::isDefault($parameter->required)) {
                    $parameter->required = !$schema->isNullable();
                }
            }

            if (!Generator::isDefault($parameter->schema)) {
                $this->generator->getTypeResolver()->mapNativeType($parameter->schema, $parameter->schema->type);
            }
        }
    }

    /**
     * Use the parameter->name as key field (parameter->parameter) when used as reusable component
     * (openapi->components->parameters).
     */
    protected function augmentSharedParameters(Analysis $analysis): void
    {
        if (!Generator::isDefault($analysis->openapi->components) && !Generator::isDefault($analysis->openapi->components->parameters)) {
            $keys = [];
            $parametersWithoutKey = [];
            foreach ($analysis->openapi->components->parameters as $parameter) {
                if (!Generator::isDefault($parameter->parameter)) {
                    $keys[$parameter->parameter] = $parameter;
                } else {
                    $parametersWithoutKey[] = $parameter;
                }
            }
            foreach ($parametersWithoutKey as $parameter) {
                if (!Generator::isDefault($parameter->name) && empty($keys[$parameter->name])) {
                    $parameter->parameter = $parameter->name;
                    $keys[$parameter->parameter] = $parameter;
                }
            }
        }
    }

    protected function augmentOperationParameters(Analysis $analysis): void
    {
        $operations = $analysis->getAnnotationsOfType(OA\Operation::class);

        foreach ($operations as $operation) {
            if (!Generator::isDefault($operation->parameters)) {
                $tags = [];
                $this->parseDocblock($operation->_context->comment, $tags);
                $docblockParams = $tags['param'] ?? [];

                foreach ($operation->parameters as $parameter) {
                    if (Generator::isDefault($parameter->description)) {
                        if (array_key_exists($parameter->name, $docblockParams)) {
                            $details = $docblockParams[$parameter->name];
                            if ($details['description']) {
                                $parameter->description = $details['description'];
                            }
                        }
                    }
                }
            }
        }
    }
}
