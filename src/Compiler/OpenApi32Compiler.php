<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Compiler;

use OpenApi\Spec as OA;
use OpenApi\Specification;

/**
 * Compiles a Specification into an OpenAPI 3.2.x document array.
 *
 * 3.2 is a superset of 3.1 — adds Tag summary/parent/kind and PathItem query.
 */
class OpenApi32Compiler extends OpenApi31Compiler
{
    protected const VERSIONS = ['3.2.0'];

    public function getVersion(): string
    {
        return '3.2.0';
    }

    public function validate(Specification $specification): array
    {
        parent::validate($specification);

        $tagNames = array_map(fn (OA\Tag $tag): ?string => $tag->name, $specification->tags);

        foreach ($specification->tags as $tag) {
            if ($tag->parent !== null && !in_array($tag->parent, $tagNames, true)) {
                $this->logger->warning('Tag "' . $tag->name . '" references non-existent parent "' . $tag->parent . '"');
                $tag->parent = null;
            }
        }

        return $this->logger->entries();
    }

    #[\Override]
    protected function compileTag(OA\Tag $tag): array
    {
        return $this->filter([
            'name' => $tag->name,
            'summary' => $tag->summary,
            'description' => $tag->description,
            'externalDocs' => $tag->externalDocs instanceof OA\ExternalDocumentation ? $this->compileExternalDocs($tag->externalDocs) : null,
            'parent' => $tag->parent,
            'kind' => $tag->kind,
        ], $tag);
    }
}
