<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Analysers;

use OpenApi\Annotations\AbstractAnnotation;
use OpenApi\Annotations\Schema;
use OpenApi\Attributes\Attachable;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\PathParameter;
use OpenApi\Attributes\Property;
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

        /** @var AbstractAnnotation[] $annotations */
        $annotations = [];
        try {
            foreach ($reflector->getAttributes() as $attribute) {
                try {
                    $instance = $attribute->newInstance();
                    if ($instance instanceof AbstractAnnotation) {
                        $annotations[] = $instance;
                    }
                } catch (\Error $e) {
                    $context->logger->debug('Could not instantiate attribute: ' . $e->getMessage(), ['exception' => $e]);
                }
            }

            if ($reflector instanceof \ReflectionMethod) {
                // also look at parameter attributes
                foreach ($reflector->getParameters() as $rp) {
                    foreach ([Property::class, Parameter::class, PathParameter::class] as $attributeName) {
                        foreach ($rp->getAttributes($attributeName) as $attribute) {
                            $instance = $attribute->newInstance();
                            $type = (($rnt = $rp->getType()) && $rnt instanceof \ReflectionNamedType) ? $rnt->getName() : Generator::UNDEFINED;
                            if ($instance instanceof Property) {
                                $instance->property = $rp->getName();
                                if (Generator::isDefault($instance->type)) {
                                    $instance->type = $type;
                                }
                            } else {
                                $instance->name = $rp->getName();
                                $instance->merge([new Schema(['type' => $type, '_context' => new Context(['nested' => $this], $context)])]);
                            }
                            $annotations[] = $instance;
                        }
                    }
                }
            }
        } finally {
            Generator::$context = null;
        }

        $annotations = array_values(array_filter($annotations, function ($a) {
            return $a !== null && $a instanceof AbstractAnnotation;
        }));

        // merge backwards into parents...
        $isParent = function (AbstractAnnotation $annotation, AbstractAnnotation $possibleParent): bool {
            // regular annotation hierarchy
            $explicitParent = null !== $possibleParent::matchNested(get_class($annotation));

            $isParentAllowed = false;
            // support Attachable subclasses
            if ($isAttachable = $annotation instanceof Attachable) {
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
            return get_class($annotation) != get_class($possibleParent)
                && ($explicitParent || ($isAttachable && $isParentAllowed));
        };
        foreach ($annotations as $index => $annotation) {
            for ($ii = 0; $ii < count($annotations); ++$ii) {
                if ($ii === $index) {
                    continue;
                }
                $possibleParent = $annotations[$ii];
                if ($isParent($annotation, $possibleParent)) {
                    $possibleParent->merge([$annotation]);
                }
            }
        }

        $annotations = array_filter($annotations, function ($a) {
            return !$a instanceof Attachable;
        });

        return $annotations;
    }
}
