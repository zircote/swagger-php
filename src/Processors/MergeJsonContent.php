<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Annotations\MediaType;
use OpenApi\Annotations\JsonContent;
use OpenApi\Annotations\Response;
use OpenApi\Annotations\RequestBody;
use OpenApi\Analysis;
use OpenApi\Context;

/**
 * Split JsonContent into Schema and MediaType
 */
class MergeJsonContent
{
    public function __invoke(Analysis $analysis)
    {
        $annotations = $analysis->getAnnotationsOfType(JsonContent::class);
        foreach ($annotations as $jsonContent) {
            $response = $jsonContent->_context->nested;
            if (!($response instanceof Response) && !($response instanceof RequestBody)) {
                continue;
            }
            if ($response->content === UNDEFINED) {
                $response->content = [];
            }
            $response->content['application/json'] = new MediaType(
                [
                'mediaType' => 'application/json',
                'schema' => $jsonContent,
                'example' => $jsonContent->example,
                'examples' => $jsonContent->examples,
                '_context' => new Context(['generated' => true], $jsonContent->_context)
                ]
            );
            $jsonContent->example = UNDEFINED;
            $jsonContent->examples = UNDEFINED;

            $index = array_search($jsonContent, $response->_unmerged, true);
            if ($index !== false) {
                array_splice($response->_unmerged, $index, 1);
            }
        }
    }
}
