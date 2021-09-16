<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Analysers;

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
        if (\PHP_VERSION_ID < 80100) {
            return [];
        }

        // no proper way to inject
        Generator::$context = $context;
        $annotations = [];
        try {
            foreach ($reflector->getAttributes() as $attribute) {
                $instance = $attribute->newInstance();
                $annotations[] = $instance;
            }
        } finally {
            Generator::$context = null;
        }

        return array_filter($annotations, function ($a) {
            return $a !== null;
        });
    }
}
