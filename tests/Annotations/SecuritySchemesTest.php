<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Annotations;

use OpenApi\Annotations as OA;
use OpenApi\Tests\OpenApiTestCase;

/**
 * Class SecuritySchemesTest.
 *
 * Security openapi test
 */
class SecuritySchemesTest extends OpenApiTestCase
{
    /**
     * Test parse servers.
     */
    public function testParseServers(): void
    {
        $comment = <<<INFO
/**
 * @OA\Info(
 *     title="Simple api",
 *     description="Simple api description",
 * )
 * @OA\Server(
 *     url="http://example.com",
 *     description="First host"
 * )
 * @OA\Server(
 *     url="http://example-second.com",
 *     description="Second host"
 * )
 */

INFO;
        $annotations = $this->annotationsFromDocBlockParser($comment);

        $this->assertCount(3, $annotations);
        $this->assertInstanceOf(OA\Info::class, $annotations[0]);
        $this->assertInstanceOf(OA\Server::class, $annotations[1]);
        $this->assertInstanceOf(OA\Server::class, $annotations[2]);

        $this->assertEquals('http://example.com', $annotations[1]->url);
        $this->assertEquals('First host', $annotations[1]->description);

        $this->assertEquals('http://example-second.com', $annotations[2]->url);
        $this->assertEquals('Second host', $annotations[2]->description);
    }

    /**
     * Test parse security scheme.
     */
    public function testImplicitFlowAnnotation(): void
    {
        $comment = <<<SCHEME
/**
 * @OA\SecurityScheme(
 *     @OA\Flow(
 *         flow="implicit",
 *         tokenUrl="http://auth.test.com/token",
 *         refreshUrl="http://auth.test.com/refresh-token"
 *     ),
 *     securityScheme="oauth2",
 *     in="header",
 *     type="oauth2",
 *     description="Oauth2 security",
 *     name="oauth2",
 *     scheme="https",
 *     bearerFormat="bearer",
 *     openIdConnectUrl="http://test.com",
 * )
 */
SCHEME;

        $annotations = $this->annotationsFromDocBlockParser($comment);
        $this->assertCount(1, $annotations);
        /** @var \OpenApi\Annotations\SecurityScheme $security */
        $security = $annotations[0];
        $this->assertInstanceOf(OA\SecurityScheme::class, $security);

        $this->assertCount(1, $security->flows);
        $this->assertEquals('implicit', $security->flows[0]->flow);
        $this->assertEquals('http://auth.test.com/token', $security->flows[0]->tokenUrl);
        $this->assertEquals('http://auth.test.com/refresh-token', $security->flows[0]->refreshUrl);
    }

    public function testMultipleAnnotations(): void
    {
        $comment = <<<SCHEME
/**
 * @OA\SecurityScheme(
 *     @OA\Flow(
 *         flow="implicit",
 *         tokenUrl="http://auth.test.com/token",
 *         refreshUrl="http://auth.test.com/refresh-token"
 *     ),
 *     @OA\Flow(
 *         flow="client_credentials",
 *         authorizationUrl="http://authClient.test.com",
 *         tokenUrl="http://authClient.test.com/token",
 *         refreshUrl="http://authClient.test.com/refresh-token"
 *     ),
 *     securityScheme="oauth2",
 *     in="header",
 *     type="oauth2",
 *     description="Oauth2 security",
 *     name="oauth2",
 *     scheme="https",
 *     bearerFormat="bearer",
 *     openIdConnectUrl="http://test.com",
 * )
 */
SCHEME;

        $annotations = $this->annotationsFromDocBlockParser($comment);
        $this->assertCount(1, $annotations);
        /** @var \OpenApi\Annotations\SecurityScheme $security */
        $security = $annotations[0];

        $this->assertCount(2, $security->flows);
        $this->assertEquals('implicit', $security->flows[0]->flow);
        $this->assertEquals('http://auth.test.com/token', $security->flows[0]->tokenUrl);
        $this->assertEquals('http://auth.test.com/refresh-token', $security->flows[0]->refreshUrl);
        $this->assertEquals('client_credentials', $security->flows[1]->flow);
        $this->assertEquals('http://authClient.test.com', $security->flows[1]->authorizationUrl);
        $this->assertEquals('http://authClient.test.com/token', $security->flows[1]->tokenUrl);
        $this->assertEquals('http://authClient.test.com/refresh-token', $security->flows[1]->refreshUrl);
    }
}
