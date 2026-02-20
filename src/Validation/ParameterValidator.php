<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Validation;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Generator;

/**
 * @extends AbstractValidator<OA\Parameter>
 */
class ParameterValidator extends AbstractValidator
{
    public function validate(Analysis $analysis, OA\AbstractAnnotation $root, \stdClass $context): bool
    {
        return $this->validateType($root);
    }

    protected function validateType(OA\Parameter $root): bool
    {
        $isValid = true;

        if (Generator::isDefault($root->ref)) {
            if ($root->in === 'body') {
                if (Generator::isDefault($root->schema)) {
                    $this->logger->warning('Field "schema" is required when ' . $root->identity() . ' is in "' . $root->in . '" in ' . $root->_context);
                    $isValid = false;
                }
            }
        }

        return $isValid;
    }
}
