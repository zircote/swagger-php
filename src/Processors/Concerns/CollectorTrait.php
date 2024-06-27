<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors\Concerns;

use OpenApi\Annotations as OA;

trait CollectorTrait
{
    /**
     * Collects a (complete) list of all nested/referenced annotations starting from the given root.
     */
    public function collect(iterable $root): \SplObjectStorage
    {
        $storage = new \SplObjectStorage();

        $this->traverse($root, function (OA\AbstractAnnotation $annotation) use (&$storage) {
            $storage->attach($annotation);
        });

        return $storage;
    }

    /**
     * @param string|array|OA\AbstractAnnotation $root
     */
    public function traverse($root, callable $callable): void
    {
        if (is_iterable($root)) {
            foreach ($root as $value) {
                $this->traverse($value, $callable);
            }
        } elseif ($root instanceof OA\AbstractAnnotation) {
            $callable($root);

            foreach (array_merge($root::$_nested, ['allOf', 'anyOf', 'oneOf', 'callbacks']) as $properties) {
                foreach ((array) $properties as $property) {
                    if (isset($root->{$property})) {
                        $this->traverse($root->{$property}, $callable);
                    }
                }
            }
        }
    }
}
