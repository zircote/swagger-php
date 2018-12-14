<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Annotations\MediaType;
use OpenApi\Annotations\RequestBody;
use OpenApi\Annotations\Response;
use OpenApi\Analysis;
use OpenApi\Annotations\XmlContent;
use OpenApi\Context;

/**
 * Split XmlContent into Schema and MediaType
 */
class MergeXmlContent
{
    public function __invoke(Analysis $analysis)
    {
        $annotations = $analysis->getAnnotationsOfType(XmlContent::class);
        foreach ($annotations as $xmlContent) {
            $response = $xmlContent->_context->nested;
            if (!($response instanceof Response) && !($response instanceof RequestBody)) {
                continue;
            }
            if ($response->content === UNDEFINED) {
                $response->content = [];
            }
            $response->content['application/xml'] = new MediaType(
                [
                    'mediaType' => 'application/xml',
                    'schema' => $xmlContent,
                    'example' => $xmlContent->example,
                    'examples' => $xmlContent->examples,
                    '_context' => new Context(['generated' => true], $xmlContent->_context)
                ]
            );
            $xmlContent->example = UNDEFINED;
            $xmlContent->examples = UNDEFINED;

            $index = array_search($xmlContent, $response->_unmerged, true);
            if ($index !== false) {
                array_splice($response->_unmerged, $index, 1);
            }
        }
    }
}
