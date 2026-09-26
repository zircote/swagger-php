<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Analysers;

use OpenApi\Annotations as OA;
use OpenApi\Context;
use OpenApi\Generator;
use OpenApi\GeneratorAwareTrait;

/**
 * Builds annotations from docblock comments.
 *
 * @deprecated since 6.11, removed in 8.0 - use attributes instead
 */
class DocBlockAnnotationFactory implements AnnotationFactoryInterface
{
    use GeneratorAwareTrait;

    protected ?DocBlockParser $docBlockParser = null;

    /** Reset per generator run, so a scan of a thousand annotated files reports once. */
    protected bool $deprecationReported = false;

    public function __construct(?DocBlockParser $docBlockParser = null)
    {
        $this->docBlockParser = $docBlockParser ?: new DocBlockParser();
    }

    public function isSupported(): bool
    {
        return DocBlockParser::isEnabled();
    }

    public function setGenerator(Generator $generator): static
    {
        $this->generator = $generator;
        $this->deprecationReported = false;

        $this->docBlockParser->setAliases($generator->getAliases());

        return $this;
    }

    public function build(\Reflector $reflector, Context $context): array
    {
        $aliases = $this->generator instanceof Generator ? $this->generator->getAliases() : [];

        if (method_exists($reflector, 'getShortName') && method_exists($reflector, 'getName')) {
            $aliases[strtolower((string) $reflector->getShortName())] = $reflector->getName();
        }

        if ($context->with('scanned') instanceof Context) {
            $details = $context->scanned;
            foreach ($details['uses'] as $alias => $name) {
                $aliasKey = strtolower((string) $alias);
                if ($name != $alias && !array_key_exists($aliasKey, $aliases)) {
                    // real aliases only
                    $aliases[strtolower((string) $alias)] = $name;
                }
            }
        }
        $this->docBlockParser->setAliases($aliases);

        if (method_exists($reflector, 'getDocComment') && ($comment = $reflector->getDocComment())) {
            $annotations = [];
            foreach ($this->docBlockParser->fromComment($comment, $context) as $instance) {
                if ($instance instanceof OA\AbstractAnnotation) {
                    $this->reportDeprecation();
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

    /**
     * Reported on the first docblock annotation actually parsed, not on the first docblock
     * seen: a project that has migrated to attributes still has docblocks everywhere.
     */
    protected function reportDeprecation(): void
    {
        if ($this->deprecationReported) {
            return;
        }

        $this->deprecationReported = true;
        trigger_deprecation('zircote/swagger-php', '6.11', 'Docblock annotations are deprecated and will be removed in 8.0; use attributes instead');
    }
}
