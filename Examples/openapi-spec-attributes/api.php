<?php

namespace OpenApi\Examples\OpenapiSpecAttributes;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *  title="Link Example",
 *  version="1.0.0"
 * )
 */
#[OA\Info(version: '1.0.0', title: 'Link Example')]
class OpenApiSpec
{
}