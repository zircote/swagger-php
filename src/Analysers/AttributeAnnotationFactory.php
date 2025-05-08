<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Analysers;

use OpenApi\Annotations as OA;
use OpenApi\Context;
use OpenApi\Generator;
use OpenApi\GeneratorAwareTrait;

class AttributeAnnotationFactory implements AnnotationFactoryInterface
{
    use GeneratorAwareTrait;

    protected bool $ignoreOtherAttributes = false;

    public function __construct(bool $ignoreOtherAttributes = false)
    {
        $this->ignoreOtherAttributes = $ignoreOtherAttributes;
    }

    public function isSupported(): bool
    {
        return \PHP_VERSION_ID >= 80100;
    }

    public function build(\Reflector $reflector, Context $context): array
    {
        if (!$this->isSupported() || !method_exists($reflector, 'getAttributes')) {
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
            $attributeName = $this->ignoreOtherAttributes
                ? [OA\AbstractAnnotation::class, \ReflectionAttribute::IS_INSTANCEOF]
                : [];

            foreach ($reflector->getAttributes(...$attributeName) as $attribute) {
                if (class_exists($attribute->getName())) {
                    $instance = $attribute->newInstance();
                    if ($instance instanceof OA\AbstractAnnotation) {
                        $annotations[] = $instance;
                    } else {
                        if ($context->is('other') === false) {
                            $context->other = [];
                        }
                        $context->other[] = $instance;
                    }
                } else {
                    $context->logger->debug(sprintf('Could not instantiate attribute "%s"; class not found.', $attribute->getName()));
                }
            }

            if ($reflector instanceof \ReflectionMethod) {
                // also look at parameter attributes
                foreach ($reflector->getParameters() as $rp) {
                    foreach ([OA\Property::class, OA\Parameter::class, OA\RequestBody::class] as $attributeName) {
                        foreach ($rp->getAttributes($attributeName, \ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                            /** @var OA\Property|OA\Parameter|OA\RequestBody $instance */
                            $instance = $attribute->newInstance();
                            $instance->_context = new Context(['nested' => false], $context);

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
                                    // ensure each property has its own context
                                    $instance->_context = new Context(['generated' => true, 'annotations' => [$instance]], $context);

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

            // Attachables can always be nested (unless explicitly restricted)
            return ($isAttachable && $isParentAllowed)
                || ($annotation->getRoot() !== $possibleParent->getRoot() && $explicitParent);
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
