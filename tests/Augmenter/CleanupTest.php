<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Augmenter;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Tests\Concerns\ExpectsLogEntries;
use PHPUnit\Framework\TestCase;

final class CleanupTest extends TestCase
{
    use ExpectsLogEntries;

    public function testRemovesUnreferencedSchema(): void
    {
        $spec = new Specification();

        $usedSchema = new OA\Schema(schema: 'Used');
        $unusedSchema = new OA\Schema(schema: 'Unused');
        $spec->schemas = [$usedSchema, $unusedSchema];

        $operation = new OA\Operation(path: '/test', method: 'get');
        $response = new OA\Response(response: 200, description: 'OK', content: [
            new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(ref: '#/components/schemas/Used')),
        ]);
        $operation->responses = [$response];
        $spec->operations[] = $operation;

        (new Augmenter\Cleanup())($spec);

        $this->assertCount(1, $spec->schemas);
        $this->assertSame('Used', $spec->schemas[0]->schema);
    }

    public function testKeepsReferencedSchemaInProperty(): void
    {
        $spec = new Specification();

        $childSchema = new OA\Schema(schema: 'Child');
        $parentSchema = new OA\Schema(schema: 'Parent', properties: [
            new OA\Property(property: 'child', schema: new OA\Schema(ref: '#/components/schemas/Child')),
        ]);
        $spec->schemas = [$childSchema, $parentSchema];

        $operation = new OA\Operation(path: '/test', method: 'get');
        $response = new OA\Response(response: 200, description: 'OK', content: [
            new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(ref: '#/components/schemas/Parent')),
        ]);
        $operation->responses = [$response];
        $spec->operations[] = $operation;

        (new Augmenter\Cleanup())($spec);

        $this->assertCount(2, $spec->schemas);
    }

    public function testRemovesNestedDependencies(): void
    {
        $spec = new Specification();

        $deepSchema = new OA\Schema(schema: 'Deep');
        $middleSchema = new OA\Schema(schema: 'Middle', properties: [
            new OA\Property(property: 'deep', schema: new OA\Schema(ref: '#/components/schemas/Deep')),
        ]);
        $usedSchema = new OA\Schema(schema: 'Used');
        $spec->schemas = [$deepSchema, $middleSchema, $usedSchema];

        $operation = new OA\Operation(path: '/test', method: 'get');
        $response = new OA\Response(response: 200, description: 'OK', content: [
            new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(ref: '#/components/schemas/Used')),
        ]);
        $operation->responses = [$response];
        $spec->operations[] = $operation;

        (new Augmenter\Cleanup())($spec);

        $this->assertCount(1, $spec->schemas);
        $this->assertSame('Used', $spec->schemas[0]->schema);
    }

    public function testDisabledDoesNothing(): void
    {
        $spec = new Specification();
        $spec->schemas = [new OA\Schema(schema: 'Orphan')];

        $augmenter = new Augmenter\Cleanup(enabled: false);
        $augmenter($spec);

        $this->assertCount(1, $spec->schemas);
    }

    public function testKeepsSchemaReferencedViaAllOf(): void
    {
        $spec = new Specification();

        $baseSchema = new OA\Schema(schema: 'Base');
        $compositeSchema = new OA\Schema(schema: 'Composite', allOf: [
            new OA\Schema(ref: '#/components/schemas/Base'),
        ]);
        $spec->schemas = [$baseSchema, $compositeSchema];

        $operation = new OA\Operation(path: '/test', method: 'get');
        $response = new OA\Response(response: 200, description: 'OK', content: [
            new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(ref: '#/components/schemas/Composite')),
        ]);
        $operation->responses = [$response];
        $spec->operations[] = $operation;

        (new Augmenter\Cleanup())($spec);

        $this->assertCount(2, $spec->schemas);
    }

    public function testKeepsSchemaReferencedViaDiscriminatorMapping(): void
    {
        $spec = new Specification();

        $catSchema = new OA\Schema(schema: 'Cat');
        $dogSchema = new OA\Schema(schema: 'Dog');
        $petSchema = new OA\Schema(schema: 'Pet', discriminator: new OA\Discriminator(propertyName: 'type', mapping: [
            'cat' => '#/components/schemas/Cat',
            'dog' => '#/components/schemas/Dog',
        ]));
        $spec->schemas = [$catSchema, $dogSchema, $petSchema];

        $operation = new OA\Operation(path: '/pets', method: 'get');
        $response = new OA\Response(response: 200, description: 'OK', content: [
            new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(ref: '#/components/schemas/Pet')),
        ]);
        $operation->responses = [$response];
        $spec->operations[] = $operation;

        (new Augmenter\Cleanup())($spec);

        $this->assertCount(3, $spec->schemas);
    }

    public function testRemovesUnusedResponse(): void
    {
        $spec = new Specification();

        $spec->responses = [
            new OA\Response(response: 'NotFound', description: 'Not found'),
        ];

        $operation = new OA\Operation(path: '/test', method: 'get');
        $operation->responses = [new OA\Response(response: 200, description: 'OK')];
        $spec->operations[] = $operation;

        (new Augmenter\Cleanup())($spec);

        $this->assertCount(0, $spec->responses);
    }

    public function testKeepsSecuritySchemeReferencedByOperation(): void
    {
        $spec = new Specification();

        $scheme = new OA\Security\Scheme(securityScheme: 'bearerAuth', type: 'http');
        $unusedScheme = new OA\Security\Scheme(securityScheme: 'unused', type: 'http');
        $spec->securitySchemes = [$scheme, $unusedScheme];

        $operation = new OA\Operation(path: '/test', method: 'get');
        $operation->security = [new OA\Security\Requirement(scheme: 'bearerAuth')];
        $operation->responses = [new OA\Response(response: 200, description: 'OK')];
        $spec->operations[] = $operation;

        (new Augmenter\Cleanup())($spec);

        $this->assertCount(1, $spec->securitySchemes);
        $this->assertSame('bearerAuth', $spec->securitySchemes[0]->securityScheme);
    }

    public function testKeepsSecuritySchemeReferencedGlobally(): void
    {
        $spec = new Specification();

        $scheme = new OA\Security\Scheme(securityScheme: 'apiKey', type: 'apiKey');
        $spec->securitySchemes = [$scheme];
        $spec->openapi->security = [new OA\Security\Requirement(scheme: 'apiKey')];

        $operation = new OA\Operation(path: '/test', method: 'get');
        $operation->responses = [new OA\Response(response: 200, description: 'OK')];
        $spec->operations[] = $operation;

        (new Augmenter\Cleanup())($spec);

        $this->assertCount(1, $spec->securitySchemes);
    }

    public function testReportsAResponseComponentNamedAfterAStatusCode(): void
    {
        $spec = new Specification();
        $spec->responses = [new OA\Response(response: 200, description: 'OK')];

        $this->expectLogEntry('Response "200" is a component named after a status code', 'warning');
        $this->allowLogEntry('unreferenced component');

        $cleanup = new Augmenter\Cleanup();
        $cleanup->setLogger($this->trackingLogger());
        $cleanup($spec);

        $this->assertSame([], $spec->responses);
    }

    public function testReportsARangeAndDefaultTheSameWay(): void
    {
        $spec = new Specification();
        $spec->responses = [
            new OA\Response(response: '2XX', description: 'OK'),
            new OA\Response(response: 'default', description: 'fallback'),
        ];

        $this->expectLogEntry('Response "2XX" is a component named after a status code', 'warning');
        $this->expectLogEntry('Response "default" is a component named after a status code', 'warning');
        $this->allowLogEntry('unreferenced component');

        $cleanup = new Augmenter\Cleanup();
        $cleanup->setLogger($this->trackingLogger());
        $cleanup($spec);

        $this->assertSame([], $spec->responses);
    }

    /**
     * An unreferenced reusable response is ordinary — a library may declare more than any
     * one document uses — so it is only counted in the summary, not warned about, unless the
     * key looks like a status code.
     */
    public function testReportsAnUnreferencedNamedResponseOnlyInTheSummary(): void
    {
        $spec = new Specification();
        $spec->responses = [new OA\Response(response: 'NotFound', description: 'nope')];

        $this->expectLogEntry('Removed 1 unreferenced component; set cleanup.enabled to false to keep it', 'notice');
        $this->expectLogEntry('Removed unreferenced component #/components/responses/NotFound', 'debug');

        $cleanup = new Augmenter\Cleanup();
        $cleanup->setLogger($this->trackingLogger());
        $cleanup($spec);

        $this->assertSame([], $spec->responses);
    }

    public function testKeepsAndDoesNotReportAReferencedStatusCodeKey(): void
    {
        $spec = new Specification();
        $spec->responses = [new OA\Response(response: 200, description: 'OK')];

        $operation = new OA\Operation(path: '/test', method: 'get');
        $operation->responses = [new OA\Response(response: 404, ref: '#/components/responses/200')];
        $spec->operations[] = $operation;

        $cleanup = new Augmenter\Cleanup();
        $cleanup->setLogger($this->trackingLogger());
        $cleanup($spec);

        $this->assertCount(1, $spec->responses);
    }

    /**
     * Removal cascades over several passes; the summary counts every pass, once.
     */
    public function testReportsACascadingRemovalOnce(): void
    {
        $spec = new Specification();
        $spec->schemas = [
            new OA\Schema(schema: 'Deep'),
            new OA\Schema(schema: 'Middle', properties: [
                new OA\Property(property: 'deep', schema: new OA\Schema(ref: '#/components/schemas/Deep')),
            ]),
        ];

        $this->expectLogEntry('Removed 2 unreferenced components; set cleanup.enabled to false to keep them', 'notice');
        $this->expectLogEntry('Removed unreferenced component #/components/schemas/Middle', 'debug');
        $this->expectLogEntry('Removed unreferenced component #/components/schemas/Deep', 'debug');

        $cleanup = new Augmenter\Cleanup();
        $cleanup->setLogger($this->trackingLogger());
        $cleanup($spec);

        $this->assertSame([], $spec->schemas);
    }
}
