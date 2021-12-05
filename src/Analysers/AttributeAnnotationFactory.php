<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Analysers;

use OpenApi\Annotations\AbstractAnnotation;
use OpenApi\Annotations\Attachable;
use OpenApi\Annotations\PathParameter;
use OpenApi\Annotations\Schema;
use OpenApi\Context;
use OpenApi\Generator;

class AttributeAnnotationFactory implements AnnotationFactoryInterface
{
    /** @var Generator */
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

        // no proper way to inject
        Generator::$context = $context;

        /** @var AbstractAnnotation[] $annotations */
        $annotations = [];
        try {
            foreach ($reflector->getAttributes() as $attribute) {
                $instance = $attribute->newInstance();
                $annotations[] = $instance;
            }
            if ($reflector instanceof \ReflectionMethod) {
                // also look at parameter attributes
                foreach ($reflector->getParameters() as $rp) {
                    foreach ($rp->getAttributes(PathParameter::class) as $attribute) {
                        $instance = $attribute->newInstance();
                        $instance->name = $rp->getName();
                        if (($rnt = $rp->getType()) && $rnt instanceof \ReflectionNamedType) {
                            $instance->schema = new Schema(['type' => $rnt->getName(), '_context' => new Context(['nested' => $this], $context)]);
                        }
                        $annotations[] = $instance;
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
            // regular anootation hierachy
            $explicitParent = array_key_exists(get_class($annotation), $possibleParent::$_nested);

            $isParentAllowed = false;
            // support Attachable subclasses
            if ($isAttachable = $annotation instanceof Attachable && array_key_exists(Attachable::class, $possibleParent::$_nested)) {
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

            return $explicitParent || ($isAttachable && $isParentAllowed);
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
