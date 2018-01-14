<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace Swagger\Processors;

use Swagger\Annotations\MediaType;
use Swagger\Annotations\JsonContent;
use Swagger\Annotations\Response;
use Swagger\Analysis;
use Swagger\Annotations\XmlContent;
use Swagger\Context;

/**
 * Split JsonContent into Schema and MediaType
 */
class MergeXmlContent
{
    public function __invoke(Analysis $analysis)
    {
        $annotations = $analysis->getAnnotationsOfType(XmlContent::class);
        foreach ($annotations as $xmlContent) {
            $response = $xmlContent->_context->nested;
            if (!($response instanceof Response)) {
                continue;
            }
            if ($response->content === null) {
                $response->content = [];
            }
            $response->content['application/xml'] = new MediaType([
                'mediaType' => 'application/xml',
                'schema' => $xmlContent,
                'examples' => $xmlContent->examples,
                '_context' => new Context(['generated' => true], $xmlContent->_context)
            ]);
            $xmlContent->examples = null;

            $index = array_search($xmlContent, $response->_unmerged, true);
            if ($index !== false) {
                array_splice($response->_unmerged, $index, 1);
            }
        }
    }
}
