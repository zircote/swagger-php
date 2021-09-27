<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Analysers;

use OpenApi\Context;
use OpenApi\Generator;

class DocBlockAnnotationFactory implements AnnotationFactoryInterface
{
    /** @var DocBlockParser */
    protected $docBlockParser = null;

    /** @var Generator */
    protected $generator;

    public function __construct(?DocBlockParser $docBlockParser = null)
    {
        $this->docBlockParser = $docBlockParser ?: new DocBlockParser();
    }

    public function setGenerator(Generator $generator): void
    {
        $this->generator = $generator;

        $this->docBlockParser->setAliases($generator->getAliases());
    }

    public function build(\Reflector $reflector, Context $context): array
    {
        if ($comment = $reflector->getDocComment()) {
            return $this->docBlockParser->fromComment($comment, $context);
        }

        return [];
    }
}
