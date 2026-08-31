<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Utils;

/**
 * Declares a pipe constructor parameter as a configurable setting.
 *
 * `Pipeline::getConfig()` reports the current value of any parameter carrying this
 * attribute (via its matching `set*()` method) under `-D`/`-c` and the CLI config file;
 * the description is what the generated reference docs show for it.
 */
#[\Attribute(\Attribute::TARGET_PARAMETER)]
class Config
{
    public function __construct(
        public string $description,
    ) {
    }

    /**
     * This attribute's instances on $rc's constructor parameters, keyed by parameter name.
     *
     * @return array<string,self>
     */
    public static function forConstructor(\ReflectionClass $rc): array
    {
        if (!$rc->hasMethod('__construct')) {
            return [];
        }

        $found = [];
        foreach ($rc->getMethod('__construct')->getParameters() as $parameter) {
            $attributes = $parameter->getAttributes(self::class);
            if ($attributes !== []) {
                $found[$parameter->getName()] = $attributes[0]->newInstance();
            }
        }

        return $found;
    }
}
