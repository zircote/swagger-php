<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors\Concerns;

use OpenApi\Annotations as OA;
use OpenApi\Attributes as OAT;
use OpenApi\Utils\DocBlockParser;

trait DocblockTrait
{
    private ?DocBlockParser $docBlockParser = null;

    private function docBlockParser(): DocBlockParser
    {
        return $this->docBlockParser ??= new DocBlockParser();
    }

    /**
     * An annotation is a docblock root if it is the top-level / outermost annotation in a PHP docblock.
     */
    public function isDocblockRoot(OA\AbstractAnnotation $annotation): bool
    {
        if (1 == count($annotation->_context->annotations)) {
            return true;
        }

        /** @var array<class-string,bool> $matchPriorityMap */
        $matchPriorityMap = [
            OA\OpenApi::class,

            OA\Operation::class => false,
            OA\Property::class => false,
            OA\Parameter::class => false,
            OA\Response::class => false,

            OA\Schema::class => true,
            OAT\Schema::class => true,
        ];
        // try to find the best root match
        foreach ($matchPriorityMap as $className => $strict) {
            foreach ($annotation->_context->annotations as $contextAnnotation) {
                if ($strict) {
                    if ($className === $contextAnnotation::class) {
                        return $annotation === $contextAnnotation;
                    }
                } else {
                    if ($contextAnnotation instanceof $className) {
                        return $annotation === $contextAnnotation;
                    }
                }
            }
        }

        return false;
    }

    public function parseDocblock(?string $docblock, ?array &$tags = null): string
    {
        return $this->docBlockParser()->parseDocblock($docblock, $tags);
    }

    public function extractCommentSummary(string $content): string
    {
        return $this->docBlockParser()->extractCommentSummary($content);
    }

    public function extractCommentDescription(string $content): string
    {
        return $this->docBlockParser()->extractCommentDescription($content);
    }

    /**
     * @return array{type: ?string, description: ?string}
     */
    public function parseVarLine(?string $docblock): array
    {
        return $this->docBlockParser()->parseVarLine($docblock);
    }

    public function extractExampleDescription(string $docblock): ?string
    {
        return $this->docBlockParser()->extractExampleDescription($docblock);
    }

    public function isDeprecated(?string $docblock): bool
    {
        return $this->docBlockParser()->isDeprecated($docblock);
    }
}
