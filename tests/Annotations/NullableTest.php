<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Annotations;

use OpenApi\Tests\OpenApiTestCase;
use OpenApi\Annotations as OA;

class NullableTest extends OpenApiTestCase
{
    public function testTypeNullableDefined(): void
    {
        $annotations = $this->annotationsFromDocBlockParser('@OA\Schema(type="string", nullable=true)');
        $annotations[0]->validate();

        $annotations = $this->annotationsFromDocBlockParser('@OA\Schema(type="string", nullable=true)');
        $annotations[0]->_context = $this->getContext([], OA\OpenApi::VERSION_3_1_0);
        $this->assertOpenApiLogEntryContains('@OA\Schema() must not have the "nullable" property when using OpenApi version 3.1.0 in ');
        $annotations[0]->validate();
    }
}
