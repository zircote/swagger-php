<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Annotations\Examples;
use OpenApi\Annotations\Header;
use OpenApi\Annotations\Link;
use OpenApi\Annotations\Parameter;
use OpenApi\Annotations\RequestBody;
use OpenApi\Annotations\Response;
use OpenApi\Annotations\Schema;
use OpenApi\Annotations\SecurityScheme;
use OpenApi\Generator;

class AugmentRefs
{
    use Concerns\RefTrait;

    public function __invoke(Analysis $analysis)
    {
        $this->resolveAllOfRefs($analysis);
        $this->resolveFQCNRefs($analysis);
        $this->removeDuplicateRefs($analysis);
    }

    /**
     * Update refs broken due to `allOf` augmenting.
     */
    protected function resolveAllOfRefs(Analysis $analysis): void
    {
        /** @var Schema[] $schemas */
        $schemas = $analysis->getAnnotationsOfType(Schema::class);

        // ref rewriting
        $updatedRefs = [];
        foreach ($schemas as $schema) {
            if (!Generator::isDefault($schema->allOf)) {
                // do we have to keep track of properties refs that need updating?
                foreach ($schema->allOf as $ii => $allOfSchema) {
                    if (!Generator::isDefault($allOfSchema->properties)) {
                        $updatedRefs[OA\Components::ref($schema->schema . '/properties', false)] = OA\Components::ref($schema->schema . '/allOf/' . $ii . '/properties', false);
                        break;
                    }
                }
            }
        }

        if ($updatedRefs) {
            foreach ($analysis->annotations as $annotation) {
                if (property_exists($annotation, 'ref') && !Generator::isDefault($annotation->ref) && $annotation->ref !== null) {
                    foreach ($updatedRefs as $origRef => $updatedRef) {
                        if (0 === strpos($annotation->ref, $origRef)) {
                            $annotation->ref = str_replace($origRef, $updatedRef, $annotation->ref);
                        }
                    }
                }
            }
        }
    }

    protected function resolveFQCNRefs(Analysis $analysis): void
    {
        /** @var OA\AbstractAnnotation[] $annotations */
        $annotations = $analysis->getAnnotationsOfType([Examples::class, Header::class, Link::class, Parameter::class, OA\PathItem::class, RequestBody::class, Response::class, Schema::class, SecurityScheme::class]);

        foreach ($annotations as $annotation) {
            if (property_exists($annotation, 'ref') && !Generator::isDefault($annotation->ref) && is_string($annotation->ref) && !$this->isRef($annotation->ref)) {
                // check if we can resolve the ref to a component
                $resolved = false;
                foreach (OA\Components::componentTypes() as $type) {
                    if ($refSchema = $analysis->getAnnotationForSource($annotation->ref, $type)) {
                        $resolved = true;
                        $annotation->ref = OA\Components::ref($refSchema);
                    }
                }
                if (!$resolved && ($refAnnotation = $analysis->getAnnotationForSource($annotation->ref, get_class($annotation)))) {
                    $annotation->ref = OA\Components::ref($refAnnotation);
                }
            }
        }
    }

    protected function removeDuplicateRefs(Analysis $analysis): void
    {
        /** @var Schema[] $schemas */
        $schemas = $analysis->getAnnotationsOfType(Schema::class);

        foreach ($schemas as $schema) {
            if (!Generator::isDefault($schema->allOf)) {
                $refs = [];
                $dupes = [];
                foreach ($schema->allOf as $ii => $allOfSchema) {
                    if (!Generator::isDefault($allOfSchema->ref)) {
                        if (in_array($allOfSchema->ref, $refs)) {
                            $dupes[] = $allOfSchema->ref;
                            $analysis->annotations->detach($allOfSchema);
                            unset($schema->allOf[$ii]);
                            continue;
                        }
                        $refs[] = $allOfSchema->ref;
                    }
                }
                if ($dupes) {
                    $schema->allOf = array_values($schema->allOf);
                }
            }
        }
    }
}
