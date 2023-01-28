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
 * Split XmlContent into Schema and MediaType.
 */
class MergeXmlContent implements ProcessorInterface
{
    public function __invoke(Analysis $analysis)
    {
        /** @var OA\XmlContent[] $annotations */
        $annotations = $analysis->getAnnotationsOfType(OA\XmlContent::class);

        foreach ($annotations as $xmlContent) {
            $parent = $xmlContent->_context->nested;
            if (!($parent instanceof OA\Response) && !($parent instanceof OA\RequestBody) && !($parent instanceof OA\Parameter)) {
                if ($parent) {
                    $xmlContent->_context->logger->warning('Unexpected ' . $xmlContent->identity() . ' in ' . $parent->identity() . ' in ' . $parent->_context);
                } else {
                    $xmlContent->_context->logger->warning('Unexpected ' . $xmlContent->identity() . ' must be nested');
                }
                continue;
            }
            if (Generator::isDefault($parent->content)) {
                $parent->content = [];
            }
            $parent->content['application/xml'] = $mediaType = new OA\MediaType([
                'schema' => $xmlContent,
                'example' => $xmlContent->example,
                'examples' => $xmlContent->examples,
                '_context' => new Context(['generated' => true], $xmlContent->_context),
            ]);
            $analysis->addAnnotation($mediaType, $mediaType->_context);
            if (!$parent instanceof OA\Parameter) {
                $parent->content['application/xml']->mediaType = 'application/xml';
            }
            $xmlContent->example = Generator::UNDEFINED;
            $xmlContent->examples = Generator::UNDEFINED;

            $index = array_search($xmlContent, $parent->_unmerged, true);
            if ($index !== false) {
                array_splice($parent->_unmerged, $index, 1);
            }
        }
    }
}
