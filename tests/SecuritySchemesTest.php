<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

use Swagger\Analyser;
use Swagger\Annotations\Info;
use Swagger\Annotations\SecurityScheme;
use Swagger\Annotations\Server;

/**
 * Class SecuritySchemesTest
 *
 * Security openapi test
 */
class SecuritySchemesTest extends SwaggerTestCase
{
    /**
     * Test parse servers
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function testParseServers()
    {
        $comment = <<<INFO
/**
 * @OAS\Info(
 *     title="Simple api",
 *     description="Simple api description",
 * )
 * @OAS\Server(
 *     url="http://example.com",
 *     description="First host"
 * )
 * @OAS\Server(
 *     url="http://example-second.com",
 *     description="Second host"
 * )
 */

INFO;
        $analysis = $this->getAnalysis($comment);

        $this->assertCount(3, $analysis);
        $this->assertInstanceOf(Info::class, $analysis[0]);
        $this->assertInstanceOf(Server::class, $analysis[1]);
        $this->assertInstanceOf(Server::class, $analysis[2]);

        $this->assertEquals('http://example.com', $analysis[1]->url);
        $this->assertEquals('First host', $analysis[1]->description);

        $this->assertEquals('http://example-second.com', $analysis[2]->url);
        $this->assertEquals('Second host', $analysis[2]->description);
    }

    /**
     * Test parse security scheme
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function testImplicitFlowAnnotation()
    {
        $comment = <<<SCHEME
/**
 * @OAS\SecurityScheme(
 *     @OAS\Flow(
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

        $analysis = $this->getAnalysis($comment);
        $this->assertCount(1, $analysis);
        /** @var \Swagger\Annotations\SecurityScheme $security */
        $security = $analysis[0];
        $this->assertInstanceOf(SecurityScheme::class, $security);

        $this->assertCount(1, $security->flows);
        $this->assertEquals('implicit', $security->flows[0]->flow);
        $this->assertEquals('http://auth.test.com/token', $security->flows[0]->tokenUrl);
        $this->assertEquals('http://auth.test.com/refresh-token', $security->flows[0]->refreshUrl);
    }

    public function testMultipleAnnotations()
    {
        $comment = <<<SCHEME
/**
 * @OAS\SecurityScheme(
 *     @OAS\Flow(
 *         flow="implicit",
 *         tokenUrl="http://auth.test.com/token",
 *         refreshUrl="http://auth.test.com/refresh-token"
 *     ),
 *     @OAS\Flow(
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

        $analysis = $this->getAnalysis($comment);
        $this->assertCount(1, $analysis);
        /** @var \Swagger\Annotations\SecurityScheme $security */
        $security = $analysis[0];

        $this->assertCount(2, $security->flows);
        $this->assertEquals('implicit', $security->flows[0]->flow);
        $this->assertEquals('http://auth.test.com/token', $security->flows[0]->tokenUrl);
        $this->assertEquals('http://auth.test.com/refresh-token', $security->flows[0]->refreshUrl);
        $this->assertEquals('client_credentials', $security->flows[1]->flow);
        $this->assertEquals('http://authClient.test.com', $security->flows[1]->authorizationUrl);
        $this->assertEquals('http://authClient.test.com/token', $security->flows[1]->tokenUrl);
        $this->assertEquals('http://authClient.test.com/refresh-token', $security->flows[1]->refreshUrl);
    }

    /**
     * Get scheme analysis
     *
     * @param string $comment
     *
     * @return array
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    private function getAnalysis($comment)
    {
        $analyser = new Analyser();
        $analysis = $analyser->fromComment($comment, null);

        return $analysis;
    }
}
