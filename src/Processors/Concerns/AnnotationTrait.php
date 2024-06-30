<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors\Concerns;

use OpenApi\Annotations as OA;

trait AnnotationTrait
{
    /**
     * Collects a (complete) list of all nested/referenced annotations starting from the given root.
     *
     * @param string|array|iterable|OA\AbstractAnnotation $root
     */
    public function collectAnnotations($root): \SplObjectStorage
    {
        $storage = new \SplObjectStorage();

        $this->traverseAnnotations($root, function ($item) use (&$storage) {
            if ($item instanceof OA\AbstractAnnotation && !$storage->contains($item)) {
                $storage->attach($item);
            }
        });

        return $storage;
    }

    /**
     * Remove all annotations that are part of the `$annotation` tree.
     */
    public function removeAnnotation(iterable $root, OA\AbstractAnnotation $annotation): void
    {
        $remove = $this->collectAnnotations($annotation);
        $this->traverseAnnotations($root, function ($item) use ($remove) {
            if ($item instanceof \SplObjectStorage) {
                foreach ($remove as $annotation) {
                    $item->detach($annotation);
                }
            }
        });
    }

    /**
     * @param string|array|iterable|OA\AbstractAnnotation $root
     */
    public function traverseAnnotations($root, callable $callable): void
    {
        $callable($root);

        if (is_iterable($root)) {
            foreach ($root as $value) {
                $this->traverseAnnotations($value, $callable);
            }
        } elseif ($root instanceof OA\AbstractAnnotation) {
            foreach (array_merge($root::$_nested, ['allOf', 'anyOf', 'oneOf', 'callbacks']) as $properties) {
                foreach ((array) $properties as $property) {
                    if (isset($root->{$property})) {
                        $this->traverseAnnotations($root->{$property}, $callable);
                    }
                }
            }
        }
    }
}
