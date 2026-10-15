<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Augmenter;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Tests\Doubles\RecordingLogger;
use PHPUnit\Framework\TestCase;

final class SecurityRequirementsTest extends TestCase
{
    public function testDropsBareSchemeNames(): void
    {
        $requirement = new OA\Security\Requirement(scheme: 'bearer');
        $spec = new Specification();
        /* @phpstan-ignore argument.type (intentionally a bare scheme name) */
        $spec->openapi = new OA\OpenApi(security: ['bearer']);
        /* @phpstan-ignore argument.type (intentionally a bare scheme name) */
        $spec->pathItems[] = new OA\PathItem(prefix: '/users', security: ['bearer']);
        /* @phpstan-ignore argument.type (intentionally a bare scheme name) */
        $spec->operations[] = $mixed = new OA\Operation\Get(path: '/mixed', security: ['api_key', $requirement]);
        /* @phpstan-ignore argument.type (intentionally a bare scheme name) */
        $spec->operations[] = $only = new OA\Operation\Get(path: '/only', security: ['bearer']);

        $received = [];
        $augmenter = new Augmenter\SecurityRequirements();
        $augmenter->setLogger(new RecordingLogger($received));
        $augmenter($spec);

        $this->assertNull($spec->openapi->security);
        $this->assertNull($spec->pathItems[0]->security);
        $this->assertSame([$requirement], $mixed->security);
        // not `[]`, which would declare the operation public
        $this->assertNull($only->security);

        $this->assertCount(4, $received);
        $this->assertStringStartsWith(
            'Security requirement "api_key" is a bare scheme name and was dropped, use new Security\Requirement(scheme: \'api_key\') in ',
            $received[2],
        );
    }

    public function testKeepsAnEmptyList(): void
    {
        $spec = new Specification();
        $spec->operations[] = $public = new OA\Operation\Get(path: '/public', security: []);

        (new Augmenter\SecurityRequirements())($spec);

        $this->assertSame([], $public->security);
    }
}
