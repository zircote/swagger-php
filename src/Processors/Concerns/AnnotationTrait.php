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

        $this->traverseAnnotations($root, static function ($item) use (&$storage): void {
            if ($item instanceof OA\AbstractAnnotation && !$storage->offsetExists($item)) {
                $storage->offsetSet($item);
            }
        });

        return $storage;
    }

    /**
     * Remove all annotations that are part of the <code>$annotation</code> tree.
     */
    public function removeAnnotation(iterable $root, OA\AbstractAnnotation $annotation, bool $recurse = true): void
    {
        $remove = $this->collectAnnotations($annotation);
        $this->traverseAnnotations($root, static function ($item) use ($remove): void {
            if ($item instanceof \SplObjectStorage) {
                foreach ($remove as $annotation) {
                    $item->offsetUnset($annotation);
                }
            }
        }, $recurse);
    }

    /**
     * @param string|array|iterable|OA\AbstractAnnotation $root
     */
    public function traverseAnnotations($root, callable $callable, bool $recurse = true): void
    {
        $callable($root);

        if (is_iterable($root) && $recurse) {
            foreach ($root as $value) {
                $this->traverseAnnotations($value, $callable, $recurse);
            }
        } elseif ($root instanceof OA\AbstractAnnotation) {
            foreach (array_merge($root::$_nested, ['allOf', 'anyOf', 'oneOf', 'callbacks']) as $properties) {
                foreach ((array) $properties as $property) {
                    if (isset($root->{$property})) {
                        $this->traverseAnnotations($root->{$property}, $callable, $recurse);
                    }
                }
            }
        }
    }
}
