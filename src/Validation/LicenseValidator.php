<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Validation;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Generator;

/**
 * @extends AbstractValidator<OA\License>
 */
class LicenseValidator extends AbstractValidator
{
    public function validate(Analysis $analysis, OA\AbstractAnnotation $root, \stdClass $context): bool
    {
        return $this->validateForVersion($root, $this->version($analysis));
    }

    protected function validateForVersion(OA\License $root, string $version): bool
    {
        $isValid = true;

        if (!OA\OpenApi::versionMatch($version, '3.0.x')) {
            if (!Generator::isDefault($root->url) && !Generator::isDefault($root->identifier)) {
                $this->logger->warning($root->identity() . ' url and identifier are mutually exclusive in ' . $root->_context);
                $isValid = false;
            }
        }

        return $isValid;
    }
}
