<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Generator;

/**
 * Use the RequestBody context to extract useful information and inject that into the annotation.
 */
class AugmentRequestBody
{
    public function __invoke(Analysis $analysis)
    {
        /** @var array<OA\RequestBody> $requests */
        $requests = $analysis->getAnnotationsOfType(OA\RequestBody::class);

        $this->augmentRequestBody($requests);
    }

    /**
     * @param array<OA\RequestBody> $requests
     */
    protected function augmentRequestBody(array $requests): void
    {
        foreach ($requests as $request) {
            if (!$request->isRoot(OA\RequestBody::class)) {
                continue;
            }
            if (Generator::isDefault($request->request)) {
                if ($request->_context->is('class')) {
                    $request->request = $request->_context->class;
                } elseif ($request->_context->is('interface')) {
                    $request->request = $request->_context->interface;
                } elseif ($request->_context->is('trait')) {
                    $request->request = $request->_context->trait;
                } elseif ($request->_context->is('enum')) {
                    $request->request = $request->_context->enum;
                }
            }
        }
    }
}
