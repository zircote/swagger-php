<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Validation;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Generator;

/**
 * @extends AbstractValidator<OA\Operation>
 */
class OperationValidator extends AbstractValidator
{
    public function validate(Analysis $analysis, OA\AbstractAnnotation $root, \stdClass $context): bool
    {
        return $this->validateType($root, $context);
    }

    protected function validateType(OA\Operation $root, \stdClass $context): bool
    {
        $isValid = true;

        if (!Generator::isDefault($root->responses)) {
            foreach ($root->responses as $response) {
                if (!Generator::isDefault($response->response) && $response->response !== 'default' && preg_match('/^([12345]{1}\d{2})|([12345]{1}XX)$/', (string) $response->response) === 0) {
                    $this->logger->warning('Invalid value "' . $response->response . '" for ' . $response->identity([]) . '->response, expecting "default", a HTTP Status Code or HTTP Status Code range definition in ' . $response->_context);
                    $isValid = false;
                }
            }
        }

        if (!Generator::isDefault($root->operationId)) {
            if (!property_exists($context, 'operationIds')) {
                $context->operationIds = [];
            }

            if (in_array($root->operationId, $context->operationIds)) {
                $this->logger->warning('operationId must be unique. Duplicate value found: "' . $root->operationId . '"');
                $isValid = false;
            }

            $context->operationIds[] = $root->operationId;
        }

        return $isValid;
    }
}
