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
 */
final class UncoveredAttributesTest extends TestCase
{
    /**
     * @return iterable<string, array{OA\Operation, string}>
     */
    public static function typedOperations(): iterable
    {
        yield 'head' => [new OA\Operation\Head(path: '/ping'), 'head'];
        yield 'options' => [new OA\Operation\Options(path: '/ping'), 'options'];
        yield 'trace' => [new OA\Operation\Trace(path: '/ping'), 'trace'];
    }

    #[DataProvider('typedOperations')]
    public function testTypedOperationCompilesUnderItsHttpMethod(OA\Operation $operation, string $method): void
    {
        $operation->responses = [new OA\Response(response: 200, description: 'ok')];

        $compiled = (new OpenApi31Compiler())->compile((new Specification())->add($operation));

        $this->assertSame([$method], array_keys($compiled['paths']['/ping']));
    }

    /**
     * @return iterable<string, array{OA\Security\Scheme, array<string, mixed>}>
     */
    public static function securitySchemes(): iterable
    {
        yield 'mutualTLS' => [
            new OA\Security\Scheme\MutualTls(securityScheme: 'scheme'),
            ['type' => 'mutualTLS'],
        ];
        yield 'openIdConnect' => [
            new OA\Security\Scheme\OpenIdConnect(securityScheme: 'scheme', openIdConnectUrl: 'https://id.example.com'),
            ['type' => 'openIdConnect', 'openIdConnectUrl' => 'https://id.example.com'],
        ];
    }

    /**
     * @param array<string, mixed> $expected
     */
    #[DataProvider('securitySchemes')]
    public function testSecuritySchemePrefillsItsType(OA\Security\Scheme $scheme, array $expected): void
    {
        $compiled = (new OpenApi31Compiler())->compile((new Specification())->add($scheme));

        $this->assertSame($expected, $compiled['components']['securitySchemes']['scheme']);
    }

    /**
     * @return iterable<string, array{OA\Flow, string}>
     */
    public static function oauthFlows(): iterable
    {
        yield 'authorizationCode' => [
            new OA\Flow\AuthorizationCode(
                authorizationUrl: 'https://example.com/authorize',
                tokenUrl: 'https://example.com/token',
                scopes: ['read' => 'Read'],
            ),
            'authorizationCode',
        ];
        yield 'clientCredentials' => [
            new OA\Flow\ClientCredentials(tokenUrl: 'https://example.com/token', scopes: ['write' => 'Write']),
            'clientCredentials',
        ];
    }

    #[DataProvider('oauthFlows')]
    public function testOAuthFlowCompilesUnderItsFlowKey(OA\Flow $flow, string $key): void
    {
        $compiled = (new OpenApi31Compiler())->compile((new Specification())->add(
            new OA\Security\Scheme\OAuth2(securityScheme: 'oauth', flows: [$flow])
        ));

        $this->assertSame([$key], array_keys($compiled['components']['securitySchemes']['oauth']['flows']));
    }
}
