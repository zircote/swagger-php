<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Augmenter;
use OpenApi\Loggers\CollectingLogger;
use OpenApi\Spec as OA;
use OpenApi\Specification;
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

        $logger = new CollectingLogger();
        $augmenter = new Augmenter\SecurityRequirements();
        $augmenter->setLogger($logger);
        $augmenter($spec);

        $this->assertNull($spec->openapi->security);
        $this->assertNull($spec->pathItems[0]->security);
        $this->assertSame([$requirement], $mixed->security);
        // not `[]`, which would declare the operation public
        $this->assertNull($only->security);

        $this->assertCount(4, $logger->entries());
        $this->assertStringStartsWith(
            'Security requirement "api_key" is a bare scheme name and was dropped, use new Security\Requirement(scheme: \'api_key\') in ',
            $logger->entries()[2]['message'],
        );
    }

    public function testKeepsAndReportsRawArrays(): void
    {
        $spec = new Specification();
        /* @phpstan-ignore argument.type (intentionally a raw array) */
        $spec->openapi = new OA\OpenApi(security: [['bearer' => []]]);
        /* @phpstan-ignore argument.type (intentionally a raw array) */
        $spec->pathItems[] = new OA\PathItem(prefix: '/users', security: [['api_key']]);
        /* @phpstan-ignore argument.type (intentionally a raw array) */
        $spec->operations[] = $operation = new OA\Operation\Get(path: '/optional', security: [[]]);

        $logger = new CollectingLogger();
        $augmenter = new Augmenter\SecurityRequirements();
        $augmenter->setLogger($logger);
        $augmenter($spec);

        $this->assertSame([['bearer' => []]], $spec->openapi->security);
        $this->assertSame([['api_key']], $spec->pathItems[0]->security);
        $this->assertSame([[]], $operation->security);

        $messages = array_column($logger->entries(), 'message');
        $this->assertCount(3, $messages);
        $this->assertStringStartsWith(
            'Security requirement {"bearer":[]} is a raw array, use Security\Requirement (array form removed in 8.0; rector rule OpenApi\Rector\SecurityToRequirementRector rewrites it) in ',
            $messages[0],
        );
        $this->assertStringStartsWith('Security requirement ["api_key"] is a raw array', $messages[1]);
        $this->assertStringStartsWith('Security requirement [] is a raw array', $messages[2]);
    }

    public function testKeepsAnEmptyList(): void
    {
        $spec = new Specification();
        $spec->operations[] = $public = new OA\Operation\Get(path: '/public', security: []);

        (new Augmenter\SecurityRequirements())($spec);

        $this->assertSame([], $public->security);
    }
}
