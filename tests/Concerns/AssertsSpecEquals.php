<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Concerns;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * Deep-equality assertion for a whole OpenAPI document — YAML string, array, or `stdClass`
 * — order-independent for maps, order-sensitive for lists.
 *
 * Pipeline-agnostic: callers hand it whatever `toYaml()`/`toArray()`/`toJson()` their
 * pipeline produced, classic or spec. Not a substitute for {@see AssertsSchemaStructure},
 * which compares schemas structurally (allOf refs + property names as a set) rather than by
 * exact document equality — that looseness is deliberate there and would be lost here.
 */
trait AssertsSpecEquals
{
    /**
     * Compare OpenApi specs assuming strings to contain YAML.
     *
     * @param array|\stdClass|string|null $actual     The generated output
     * @param array|\stdClass|string|null $expected   The specification
     * @param bool                        $normalized flag indicating whether the inputs are already normalized or
     *                                                not
     */
    public function assertSpecEquals($actual, $expected, string $message = '', bool $normalized = false): void
    {
        $formattedValue = function ($value): string {
            if (is_bool($value)) {
                return $value ? 'true' : 'false';
            }
            if (is_numeric($value)) {
                return (string) $value;
            }
            if (is_string($value)) {
                return '"' . $value . '"';
            }
            if (is_object($value)) {
                return $value::class;
            }

            return gettype($value);
        };

        $normalizeIn = function ($in) {
            if (is_string($in)) {
                // assume YAML
                try {
                    $in = Yaml::parse($in, Yaml::PARSE_OBJECT_FOR_MAP);
                } catch (ParseException $e) {
                    $this->fail('Invalid YAML: ' . $e->getMessage() . PHP_EOL . $in);
                }
            }

            return $in;
        };

        if (!$normalized) {
            $actual = $normalizeIn($actual);
            $expected = $normalizeIn($expected);
        }

        if ($actual instanceof \stdClass && $expected === []) {
            $this->fail($message . PHP_EOL . 'Expected array ([]), got object ({}).');
        }
        if ($expected instanceof \stdClass && $actual === []) {
            $this->fail($message . PHP_EOL . 'Expected object ({}), got array ([]).');
        }
        if ($actual === [] && $expected === []) {
            return;
        }

        $isComposite = fn ($v): bool => is_iterable($v) || $v instanceof \stdClass;

        if ($isComposite($actual) && $isComposite($expected)) {
            foreach ((array) $actual as $key => $value) {
                $this->assertArrayHasKey($key, (array) $expected, $message . ': property: "' . $key . '" should be absent, but has value: ' . $formattedValue($value));
                $this->assertSpecEquals($value, ((array) $expected)[$key], $message . ' > ' . $key, true);
            }
            foreach ((array) $expected as $key => $value) {
                $this->assertArrayHasKey($key, (array) $actual, $message . ': property: "' . $key . '" is missing');
                $this->assertSpecEquals(((array) $actual)[$key], $value, $message . ' > ' . $key, true);
            }
        } else {
            $this->assertEquals($expected, $actual, $message);
        }
    }
}
