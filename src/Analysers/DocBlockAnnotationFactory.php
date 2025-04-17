<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Analysers;

use OpenApi\Annotations as OA;
use OpenApi\Context;
use OpenApi\Generator;
use OpenApi\GeneratorAwareTrait;

class DocBlockAnnotationFactory implements AnnotationFactoryInterface
{
    use GeneratorAwareTrait;

    protected ?DocBlockParser $docBlockParser = null;

    public function __construct(?DocBlockParser $docBlockParser = null)
    {
        $this->docBlockParser = $docBlockParser ?: new DocBlockParser();
    }

    public function isSupported(): bool
    {
        return DocBlockParser::isEnabled();
    }

    public function setGenerator(Generator $generator)
    {
        $this->generator = $generator;

        $this->docBlockParser->setAliases($generator->getAliases());

        return $this;
    }

    public function build(\Reflector $reflector, Context $context): array
    {
        $aliases = $this->generator ? $this->generator->getAliases() : [];

        if (method_exists($reflector, 'getShortName') && method_exists($reflector, 'getName')) {
            $aliases[strtolower($reflector->getShortName())] = $reflector->getName();
        }

        if ($context->with('scanned')) {
            $details = $context->scanned;
            foreach ($details['uses'] as $alias => $name) {
                $aliasKey = strtolower($alias);
                if ($name != $alias && !array_key_exists($aliasKey, $aliases)) {
                    // real aliases only
                    $aliases[strtolower($alias)] = $name;
                }
            }
        }
        $this->docBlockParser->setAliases($aliases);

        if (method_exists($reflector, 'getDocComment') && ($comment = $reflector->getDocComment())) {
            $annotations = [];
            foreach ($this->docBlockParser->fromComment($comment, $context) as $instance) {
                if ($instance instanceof OA\AbstractAnnotation) {
                    $annotations[] = $instance;
                } else {
                    if ($context->is('other') === false) {
                        $context->other = [];
                    }
                    $context->other[] = $instance;
                }
            }

            return $annotations;
        }

        return [];
    }
}
