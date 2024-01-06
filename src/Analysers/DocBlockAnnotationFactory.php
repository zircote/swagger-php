<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Analysers;

use OpenApi\Context;
use OpenApi\Generator;

class DocBlockAnnotationFactory implements AnnotationFactoryInterface
{
    /** @var DocBlockParser|null */
    protected $docBlockParser = null;

    /** @var Generator|null */
    protected $generator = null;

    public function __construct(?DocBlockParser $docBlockParser = null)
    {
        $this->docBlockParser = $docBlockParser ?: new DocBlockParser();
    }

    public function isSupported(): bool
    {
        return DocBlockParser::isEnabled();
    }

    public function setGenerator(Generator $generator): void
    {
        $this->generator = $generator;

        $this->docBlockParser->setAliases($generator->getAliases());
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
            return $this->docBlockParser->fromComment($comment, $context);
        }

        return [];
    }
}
