<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors\Concerns;

use OpenApi\Annotations as OA;

trait CollectorTrait
{
    /**
     * Collects a complete list of all nested/referenced annotations.
     */
    public function collect(iterable $annotations): \SplObjectStorage
    {
        $storage = new \SplObjectStorage();

        foreach ($annotations as $annotation) {
            if ($annotation instanceof OA\AbstractAnnotation) {
                $storage->addAll($this->traverse($annotation));
            }
        }

        return $storage;
    }

    public function traverse(OA\AbstractAnnotation $annotation): \SplObjectStorage
    {
        $storage = new \SplObjectStorage();

        if ($storage->contains($annotation)) {
            return $storage;
        }

        $storage->attach($annotation);

        foreach (array_merge($annotation::$_nested, ['allOf', 'anyOf', 'oneOf', 'callbacks']) as $properties) {
            foreach ((array) $properties as $property) {
                if (isset($annotation->{$property})) {
                    $storage->addAll($this->traverseNested($annotation->{$property}));
                }
            }
        }

        return $storage;
    }

    /**
     * @param string|array|OA\AbstractAnnotation $nested
     */
    protected function traverseNested($nested): \SplObjectStorage
    {
        $storage = new \SplObjectStorage();

        if (is_array($nested)) {
            foreach ($nested as $value) {
                $storage->addAll($this->traverseNested($value));
            }
        } elseif ($nested instanceof OA\AbstractAnnotation) {
            $storage->addAll($this->traverse($nested));
        }

        return $storage;
    }
}
