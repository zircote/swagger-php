<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Validation;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Generator;

/**
 * @extends AbstractValidator<OA\Schema>
 */
class SchemaValidator extends AbstractValidator
{
    public function validate(Analysis $analysis, OA\AbstractAnnotation $root, \stdClass $context): bool
    {
        $isValid = $this->validateType($root);
        $isValid = $this->validateForVersion($root, $this->version($analysis)) && $isValid;

        return $isValid;
    }

    protected function validateType(OA\Schema $root): bool
    {
        $isValid = true;

        if ($root->hasType('array') && Generator::isDefault($root->items)) {
            $this->logger->warning('@OA\\Items() is required when ' . $root->identity() . ' has type "array" in ' . $root->_context);

            $isValid = false;
        }

        return $isValid;
    }

    protected function validateForVersion(OA\Schema $root, string $version): bool
    {
        $isValid = true;

        if (OA\OpenApi::versionMatch($version, '3.0.x')) {
            if (!Generator::isDefault($root->examples)) {
                $this->logger->warning(OA\AbstractAnnotation::shorten($root) . '::examples is only allowed as of 3.1.0 in ' . $root->_context);
                $isValid = false;
            }
        }

        return $isValid;
    }
}
