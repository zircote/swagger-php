<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Context;
use OpenApi\Generator;

/**
 * Split JsonContent into Schema and MediaType.
 */
class MergeJsonContent implements ProcessorInterface
{
    public function __invoke(Analysis $analysis)
    {
        /** @var OA\JsonContent[] $annotations */
        $annotations = $analysis->getAnnotationsOfType(OA\JsonContent::class);

        foreach ($annotations as $jsonContent) {
            $parent = $jsonContent->_context->nested;
            if (!($parent instanceof OA\Response) && !($parent instanceof OA\RequestBody) && !($parent instanceof OA\Parameter)) {
                if ($parent) {
                    $jsonContent->_context->logger->warning('Unexpected ' . $jsonContent->identity() . ' in ' . $parent->identity() . ' in ' . $parent->_context);
                } else {
                    $jsonContent->_context->logger->warning('Unexpected ' . $jsonContent->identity() . ' must be nested');
                }
                continue;
            }
            if (Generator::isDefault($parent->content)) {
                $parent->content = [];
            }
            $parent->content['application/json'] = $mediaType = new OA\MediaType([
                'schema' => $jsonContent,
                'example' => $jsonContent->example,
                'examples' => $jsonContent->examples,
                '_context' => new Context(['generated' => true], $jsonContent->_context),
            ]);
            $analysis->addAnnotation($mediaType, $mediaType->_context);
            if (!$parent instanceof OA\Parameter) {
                $parent->content['application/json']->mediaType = 'application/json';
            }
            $jsonContent->example = Generator::UNDEFINED;
            $jsonContent->examples = Generator::UNDEFINED;

            $index = array_search($jsonContent, $parent->_unmerged, true);
            if ($index !== false) {
                array_splice($parent->_unmerged, $index, 1);
            }
        }
    }
}
