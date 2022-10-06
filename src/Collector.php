<?php declare(strict_types=1);

namespace OpenApi;

use OpenApi\Annotations as OA;

/**
 * Collects a complete list of all nested/referenced annotations.
 */
class Collector
{
    public function collect(\SplObjectStorage $annotations): \SplObjectStorage
    {
        $storage = new \SplObjectStorage();

        foreach ($annotations as $annotation) {
            if ($annotation instanceof OA\AbstractAnnotation) {
                $this->traverse($annotation, $storage);
            }
        }

        return $storage;
    }

    protected function traverse(OA\AbstractAnnotation $annotation, \SplObjectStorage $storage): void
    {
        if ($storage->contains($annotation)) {
            return;
        }

        $storage->attach($annotation);

        foreach (array_merge($annotation::$_nested, ['allOf', 'anyOf', 'oneOff', 'callbacks']) as $properties) {
            foreach ((array)$properties as $property) {
                if (isset($annotation->{$property})) {
                    $this->traverseNested($annotation->{$property}, $storage);
                }
            }
        }
    }

    /**
     * @param string|array|OA\AbstractAnnotation $nested
     */
    protected function traverseNested($nested, \SplObjectStorage $storage): void
    {
        if (is_array($nested)) {
            foreach ($nested as $value) {
                $this->traverseNested($value, $storage);
            }
        } elseif ($nested instanceof OA\AbstractAnnotation) {
            $this->traverse($nested, $storage);
        }
    }
}
