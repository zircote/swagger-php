<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Spec;

use OpenApi\Compiler\OpenApi31Compiler;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Attributes that no fixture or worked example happens to use, so nothing else compiles
 * them. Each case asserts the constructor pre-fills what its base class needs and that the
 * compiler emits it under the right key.
 *
 * Note the attributes are constructed inside the test, not in the provider: PHPUnit
 * evaluates providers while collecting tests, before coverage recording starts, so anything
 * built there is asserted but never counted as covered.
 */
final class UncoveredAttributesTest extends TestCase
{
    /**
     * @return iterable<string, array{class-string<OA\Operation>, string}>
     */
    public static function typedOperations(): iterable
    {
        yield 'head' => [OA\Operation\Head::class, 'head'];
        yield 'options' => [OA\Operation\Options::class, 'options'];
        yield 'trace' => [OA\Operation\Trace::class, 'trace'];
    }

    /**
     * @param class-string<OA\Operation> $class
     */
    #[DataProvider('typedOperations')]
    public function testTypedOperationCompilesUnderItsHttpMethod(string $class, string $method): void
    {
        $operation = new $class(
            path: '/ping',
            responses: [new OA\Response(response: 200, description: 'ok')],
        );

        $compiled = (new OpenApi31Compiler())->compile((new Specification())->add($operation));

        $this->assertSame([$method], array_keys($compiled['paths']['/ping']));
    }

    /**
     * @return iterable<string, array{class-string<OA\Security\Scheme>, array<string, mixed>, array<string, mixed>}>
     */
    public static function securitySchemes(): iterable
    {
        yield 'mutualTLS' => [
            OA\Security\Scheme\MutualTls::class,
            ['securityScheme' => 'scheme'],
            ['type' => 'mutualTLS'],
        ];
        yield 'openIdConnect' => [
            OA\Security\Scheme\OpenIdConnect::class,
            ['securityScheme' => 'scheme', 'openIdConnectUrl' => 'https://id.example.com'],
            ['type' => 'openIdConnect', 'openIdConnectUrl' => 'https://id.example.com'],
        ];
    }

    /**
     * @param class-string<OA\Security\Scheme> $class
     * @param array<string, mixed>             $arguments
     * @param array<string, mixed>             $expected
     */
    #[DataProvider('securitySchemes')]
    public function testSecuritySchemePrefillsItsType(string $class, array $arguments, array $expected): void
    {
        $scheme = new $class(...$arguments);

        $compiled = (new OpenApi31Compiler())->compile((new Specification())->add($scheme));

        $this->assertSame($expected, $compiled['components']['securitySchemes']['scheme']);
    }

    /**
     * @return iterable<string, array{class-string<OA\Flow>, array<string, mixed>, string}>
     */
    public static function oauthFlows(): iterable
    {
        yield 'authorizationCode' => [
            OA\Flow\AuthorizationCode::class,
            [
                'authorizationUrl' => 'https://example.com/authorize',
                'tokenUrl' => 'https://example.com/token',
                'scopes' => ['read' => 'Read'],
            ],
            'authorizationCode',
        ];
        yield 'clientCredentials' => [
            OA\Flow\ClientCredentials::class,
            ['tokenUrl' => 'https://example.com/token', 'scopes' => ['write' => 'Write']],
            'clientCredentials',
        ];
        yield 'password' => [
            OA\Flow\Password::class,
            ['tokenUrl' => 'https://example.com/token', 'scopes' => ['write' => 'Write']],
            'password',
        ];
        yield 'implicit' => [
            OA\Flow\Implicit::class,
            ['authorizationUrl' => 'https://example.com/authorize', 'scopes' => ['read' => 'Read']],
            'implicit',
        ];
    }

    /**
     * @param class-string<OA\Flow> $class
     * @param array<string, mixed>  $arguments
     */
    #[DataProvider('oauthFlows')]
    public function testOAuthFlowCompilesUnderItsFlowKey(string $class, array $arguments, string $key): void
    {
        $flow = new $class(...$arguments);

        $compiled = (new OpenApi31Compiler())->compile((new Specification())->add(
            new OA\Security\Scheme\OAuth2(securityScheme: 'oauth', flows: [$flow])
        ));

        $this->assertSame([$key], array_keys($compiled['components']['securitySchemes']['oauth']['flows']));
    }

    public function testXmlMediaTypeCompilesUnderItsContentType(): void
    {
        $response = new OA\Response(
            response: 200,
            description: 'ok',
            content: [new OA\MediaType\Xml(type: 'object')],
        );

        $compiled = (new OpenApi31Compiler())->compile((new Specification())->add($response));

        $this->assertSame(
            ['application/xml'],
            array_keys($compiled['components']['responses']['200']['content'])
        );
    }
}
