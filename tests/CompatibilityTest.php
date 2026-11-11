<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Augmenter;
use OpenApi\ComponentNamer\ShortName;
use OpenApi\Tests\Fixtures\Compat;
use OpenApi\Type\TypeResolver;
use PHPUnit\Framework\TestCase;

/**
 * Protected and public methods of classes meant to be extended keep their released signatures
 * within a major: an override written against an earlier minor still loads.
 */
final class CompatibilityTest extends TestCase
{
    public function testOverridesWithReleasedSignaturesStillLoad(): void
    {
        $this->assertInstanceOf(ShortName::class, new Compat\LegacyNamer());
        $this->assertInstanceOf(Augmenter\Types::class, new Compat\LegacyTypes());
        $this->assertInstanceOf(Augmenter\ImpliedSchemas::class, new Compat\LegacyImpliedSchemas());
        $this->assertInstanceOf(TypeResolver::class, new Compat\LegacyTypeResolver());
    }
}
