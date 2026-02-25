<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Validation;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Generator;

/**
 * @extends AbstractValidator<OA\OpenApi>
 */
class OpenApiValidator extends AbstractValidator
{
    public function validate(Analysis $analysis, OA\AbstractAnnotation $root, \stdClass $context): bool
    {
        $isValid = $this->validateType($root);
        $isValid = $this->validateForVersion($root, $this->version($analysis)) && $isValid;

        return $isValid;
    }

    protected function validateType(OA\OpenApi $root): bool
    {
        $isValid = true;

        if (!in_array($root->openapi, OA\OpenApi::SUPPORTED_VERSIONS)) {
            $this->logger->warning('Unsupported OpenAPI version "' . $root->openapi . '". Allowed versions are: ' . implode(', ', OA\OpenApi::SUPPORTED_VERSIONS));
            $isValid = false;
        }

        return $isValid;
    }

    protected function validateForVersion(OA\OpenApi $root, string $version): bool
    {
        $isValid = true;

        /* paths is optional in 3.1.x */
        if (OA\OpenApi::versionMatch($version, '3.0.x') && Generator::isDefault($root->paths)) {
            $this->logger->warning('Required @OA\PathItem() not found');
            $isValid = false;
        }

        if (OA\OpenApi::versionMatch($version, '3.1.x')
            && Generator::isDefault($root->paths)
            && Generator::isDefault($root->webhooks)
            && Generator::isDefault($root->components)
        ) {
            $this->logger->warning('At least one of @OA\PathItem(), @OA\Components() or @OA\Webhook() required');
            $isValid = false;
        }

        return $isValid;
    }
}
