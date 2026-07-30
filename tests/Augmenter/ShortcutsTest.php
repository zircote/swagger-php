<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Augmenter;
use OpenApi\Spec as OA;
use OpenApi\Tests\Concerns\AssemblesSpecification;
use OpenApi\Tests\Fixtures;
use PHPUnit\Framework\TestCase;

final class ShortcutsTest extends TestCase
{
    use AssemblesSpecification;

    public function testBasicEnumUsesNames(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\JsonController::class);

        (new Augmenter\Shortcuts())($spec);

        $json = $spec->operations[0]->requestBody->content[0];
        $this->assertSame('application/json', $json->mediaType);
        $this->assertInstanceOf(OA\Schema::class, $json->schema);
        $this->assertSame('string', $json->schema->type);
    }
}
