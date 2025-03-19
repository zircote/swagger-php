<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Generator;

/**
 * Checks if the annotation has a summary and/or description property
 * and uses the text in the comment block (above the annotations) as summary and/or description.
 *
 * Use <code>null</code>, for example: <code>@Annotation(description=null)</code>, if you don't want the annotation to have a description.
 */
class DocBlockDescriptions
{
    use Concerns\DocblockTrait;

    public function __invoke(Analysis $analysis)
    {
        /** @var OA\AbstractAnnotation $annotation */
        foreach ($analysis->annotations as $annotation) {
            if (property_exists($annotation, '_context') === false) {
                // only annotations with context
                continue;
            }

            if (!$this->isRoot($annotation)) {
                // only top-level annotations
                continue;
            }

            $hasSummary = property_exists($annotation, 'summary');
            $hasDescription = property_exists($annotation, 'description');
            if (!$hasSummary && !$hasDescription) {
                continue;
            }

            if ($hasSummary && $hasDescription) {
                $this->summaryAndDescription($annotation);
            } elseif ($hasDescription) {
                $this->description($annotation);
            }
        }
    }

    /**
     * @param OA\Operation|OA\Property|OA\Parameter|OA\Schema $annotation
     */
    protected function description(OA\AbstractAnnotation $annotation): void
    {
        if (!Generator::isDefault($annotation->description)) {
            if ($annotation->description === null) {
                $annotation->description = Generator::UNDEFINED;
            }

            return;
        }

        $annotation->description = $this->extractContent($annotation->_context->comment);
    }

    /**
     * @param OA\Operation|OA\Property|OA\Parameter|OA\Schema $annotation
     */
    protected function summaryAndDescription(OA\AbstractAnnotation $annotation): void
    {
        $ignoreSummary = !Generator::isDefault($annotation->summary);
        $ignoreDescription = !Generator::isDefault($annotation->description);
        if ($annotation->summary === null) {
            $ignoreSummary = true;
            $annotation->summary = Generator::UNDEFINED;
        }
        if ($annotation->description === null) {
            $annotation->description = Generator::UNDEFINED;
            $ignoreDescription = true;
        }
        if ($ignoreSummary && $ignoreDescription) {
            return;
        }
        if ($ignoreSummary) {
            $annotation->description = $this->extractContent($annotation->_context->comment);
        } elseif ($ignoreDescription) {
            $annotation->summary = $this->extractContent($annotation->_context->comment);
        } else {
            $annotation->summary = $this->extractSummary($annotation->_context->comment);
            $annotation->description = $this->extractDescription($annotation->_context->comment);
        }
    }
}
