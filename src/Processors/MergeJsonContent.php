<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace Swagger\Processors;

use Swagger\Annotations\MediaType;
use Swagger\Annotations\JsonContent;
use Swagger\Annotations\Response;
use Swagger\Analysis;
use Swagger\Context;

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
            if (!($response instanceof Response)) {
                continue;
            }
            if ($response->content === null) {
                $response->content = [];
            }
            $response->content['application/json'] = new MediaType([
                'mediaType' => 'application/json',
                'schema' => $jsonContent,
                'examples' => $jsonContent->examples,
                '_context' => new Context(['generated' => true], $jsonContent->_context)
            ]);
            $jsonContent->examples = null;

            $index = array_search($jsonContent, $response->_unmerged, true);
            if ($index !== false) {
                array_splice($response->_unmerged, $index, 1);
            }
        }
    }
}
