<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Validation;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Generator;

/**
 * @extends AbstractValidator<OA\Response>
 */
class ResponseValidator extends AbstractValidator
{
    public function validate(Analysis $analysis, OA\AbstractAnnotation $root, \stdClass $context): bool
    {
        return $this->validateType($root);
    }

    protected function validateType(OA\Response $root): bool
    {
        $isValid = true;

        if (Generator::isDefault($root->description) && Generator::isDefault($root->ref)) {
            $this->logger->warning($root->identity() . ' One of description or ref is required in ' . $root->_context);
            $isValid = false;
        }

        return $isValid;
    }
}
