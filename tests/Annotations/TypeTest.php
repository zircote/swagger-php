<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Annotations;

use OpenApi\Tests\OpenApiTestCase;
use OpenApi\Annotations as OA;

class TypeTest extends OpenApiTestCase
{
    public function testTypeOfString(): void
    {
        $annotations = $this->annotationsFromDocBlockParser('@OA\Schema(type="string")');
        $annotations[0]->validate();

        $annotations = $this->annotationsFromDocBlockParser('@OA\Schema(type="string")');
        $annotations[0]->_context = $this->getContext([], OA\OpenApi::VERSION_3_1_0);
        $annotations[0]->validate();
    }

    public function testTypeOfArray(): void
    {
        $annotations = $this->annotationsFromDocBlockParser('@OA\Schema(type={"string", "null"})');
        $this->assertOpenApiLogEntryContains('@OA\Schema() "type" must be of type string in OpenApi version 3.0.0, array given in ');
        $annotations[0]->validate();

        $annotations = $this->annotationsFromDocBlockParser('@OA\Schema(type={"string", "null"})');
        $annotations[0]->_context = $this->getContext([], OA\OpenApi::VERSION_3_1_0);
        $annotations[0]->validate();
    }
}
