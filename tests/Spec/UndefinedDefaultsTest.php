<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Spec;

use OpenApi\Tests\Concerns\CollectsSpecClasses;
use OpenApi\Undefined;
use PHPUnit\Framework\TestCase;

final class UndefinedDefaultsTest extends TestCase
{
    use CollectsSpecClasses;

    /**
     * `null` is a legal value for a `mixed` property, so it cannot also mean "not set".
     *
     * See `docs/dev/pipeline.md`, "`null` means unset, except where `null` is a value".
     */
    public function testMixedConstructorParametersDefaultToUndefined(): void
    {
        $failures = [];

        foreach (self::allSpecClasses() as $class) {
            $constructor = (new \ReflectionClass($class))->getConstructor();
            if (!$constructor instanceof \ReflectionMethod) {
                continue;
            }

            foreach ($constructor->getParameters() as $parameter) {
                if ((string) $parameter->getType() !== 'mixed') {
                    continue;
                }

                if (!$parameter->isDefaultValueAvailable() || $parameter->getDefaultValue() !== Undefined::UNDEFINED) {
                    $failures[] = sprintf('%s::__construct() parameter $%s is mixed but does not default to Undefined::UNDEFINED', $class, $parameter->getName());
                }
            }
        }

        $this->assertEmpty($failures, implode("\n", $failures));
    }
}
