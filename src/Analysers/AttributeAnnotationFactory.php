<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Analysers;

use OpenApi\Annotations as OA;
use OpenApi\Context;
use OpenApi\Generator;

class AttributeAnnotationFactory implements AnnotationFactoryInterface
{
    /** @var Generator|null */
    protected $generator;

    public function setGenerator(Generator $generator): void
    {
        $this->generator = $generator;
    }

    public function build(\Reflector $reflector, Context $context): array
    {
        if (\PHP_VERSION_ID < 80100 || !method_exists($reflector, 'getAttributes')) {
            return [];
        }

        if ($reflector instanceof \ReflectionProperty && method_exists($reflector, 'isPromoted') && $reflector->isPromoted()) {
            // handled via __construct() parameter
            return [];
        }

        // no proper way to inject
        Generator::$context = $context;

        /** @var OA\AbstractAnnotation[] $annotations */
        $annotations = [];
        try {
            foreach ($reflector->getAttributes() as $attribute) {
                if (class_exists($attribute->getName())) {
                    $instance = $attribute->newInstance();
                    if ($instance instanceof OA\AbstractAnnotation) {
                        $annotations[] = $instance;
                    }
                } else {
                    $context->logger->debug(sprintf('Could not instantiate attribute "%s", because class not found.', $attribute->getName()));
                }
            }

            if ($reflector instanceof \ReflectionMethod) {
                // also look at parameter attributes
                foreach ($reflector->getParameters() as $rp) {
                    foreach ([OA\Property::class, OA\Parameter::class, OA\RequestBody::class] as $attributeName) {
                        foreach ($rp->getAttributes($attributeName, \ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                            /** @var OA\Property|OA\Parameter|OA\RequestBody $instance */
                            $instance = $attribute->newInstance();
                            $type = (($rnt = $rp->getType()) && $rnt instanceof \ReflectionNamedType) ? $rnt->getName() : Generator::UNDEFINED;
                            $nullable = $rnt ? $rnt->allowsNull() : true;

                            if ($instance instanceof OA\RequestBody) {
                                $instance->required = !$nullable;
                            } elseif ($instance instanceof OA\Property) {
                                if (Generator::isDefault($instance->property)) {
                                    $instance->property = $rp->getName();
                                }
                                if (Generator::isDefault($instance->type)) {
                                    $instance->type = $type;
                                }
                                $instance->nullable = $nullable ?: Generator::UNDEFINED;

                                if ($rp->isPromoted()) {
                                    // promoted parameter - docblock is available via class/property
                                    if ($comment = $rp->getDeclaringClass()->getProperty($rp->getName())->getDocComment()) {
                                        $instance->_context->comment = $comment;
                                    }
                                }
                            } else {
                                if (!$instance->name || Generator::isDefault($instance->name)) {
                                    $instance->name = $rp->getName();
                                }
                                $instance->required = !$nullable;
                                $context = new Context(['nested' => $this], $context);
                                $context->comment = null;
                                $instance->merge([new OA\Schema(['type' => $type, '_context' => $context])]);
                            }
                            $annotations[] = $instance;
                        }
                    }
                }

                if (($rrt = $reflector->getReturnType()) && $rrt instanceof \ReflectionNamedType) {
                    foreach ($annotations as $annotation) {
                        if ($annotation instanceof OA\Property && Generator::isDefault($annotation->type)) {
                            // pick up simple return types
                            $annotation->type = $rrt->getName();
                        }
                    }
                }
            }
        } finally {
            Generator::$context = null;
        }

        $annotations = array_values(array_filter($annotations, function ($a) {
            return $a instanceof OA\AbstractAnnotation;
        }));

        // merge backwards into parents...
        $isParent = function (OA\AbstractAnnotation $annotation, OA\AbstractAnnotation $possibleParent): bool {
            // regular annotation hierarchy
            $explicitParent = null !== $possibleParent->matchNested($annotation) && !$annotation instanceof OA\Attachable;

            $isParentAllowed = false;
            // support Attachable subclasses
            if ($isAttachable = $annotation instanceof OA\Attachable) {
                if (!$isParentAllowed = (null === $annotation->allowedParents())) {
                    // check for allowed parents
                    foreach ($annotation->allowedParents() as $allowedParent) {
                        if ($possibleParent instanceof $allowedParent) {
                            $isParentAllowed = true;
                            break;
                        }
                    }
                }
            }

            // Property can be nested...
            return $annotation->getRoot() != $possibleParent->getRoot()
                && ($explicitParent || ($isAttachable && $isParentAllowed));
        };

        $annotationsWithoutParent = [];
        foreach ($annotations as $index => $annotation) {
            $mergedIntoParent = false;

            for ($ii = 0; $ii < count($annotations); ++$ii) {
                if ($ii === $index) {
                    continue;
                }
                $possibleParent = $annotations[$ii];
                if ($isParent($annotation, $possibleParent)) {
                    $mergedIntoParent = true; //
                    $possibleParent->merge([$annotation]);
                }
            }

            if (!$mergedIntoParent) {
                $annotationsWithoutParent[] = $annotation;
            }
        }

        return $annotationsWithoutParent;
    }
}
