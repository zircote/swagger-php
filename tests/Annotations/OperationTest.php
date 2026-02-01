<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Annotations;

use OpenApi\Annotations as OA;
use OpenApi\Tests\OpenApiTestCase;

final class OperationTest extends OpenApiTestCase
{
    public static function securityData(): \Iterator
    {
        yield 'empty' => [
            [],
            '/** @OA\Get(security={ }) */',
            '{"security":[]}',
        ];
        yield 'basic' => [
            [['api_key' => []]],
            '/** @OA\Get(security={ {"api_key":{}} }) */',
            '{"security":[{"api_key":[]}]}',
        ];
        yield 'optional' => [
            [[]],
            '/** @OA\Get(security={ {} }) */',
            '{"security":[{}]}',
        ];
        yield 'optional-oauth2' => [
            [[], ['petstore_auth' => ['write:pets', 'read:pets']]],
            '/** @OA\Get(security={ {}, {"petstore_auth":{"write:pets","read:pets"}} }) */',
            '{"security":[{},{"petstore_auth":["write:pets","read:pets"]}]}',
        ];
    }

    /**
     * @dataProvider securityData
     */
    public function testSecuritySerialization(array $security, string $docBlock, string $expected): void
    {
        // test with Get implementation...
        $operation = new OA\Get([
            'security' => $security,
            '_context' => $this->getContext(),
        ]);
        $flags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
        $json = $operation->toJson($flags);
        $this->assertSame($expected, $json);

        $annotations = $this->annotationsFromDocBlockParser($docBlock);
        $this->assertCount(1, $annotations);
        $json = $annotations[0]->toJson($flags);
        $this->assertEquals($expected, $json);
    }
}
