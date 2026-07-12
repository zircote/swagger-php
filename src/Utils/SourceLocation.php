<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Utils;

final readonly class SourceLocation implements \Stringable
{
    public function __construct(
        public ?string $filename = null,
        public ?int $line = null,
        public ?string $namespace = null,
        public ?string $class = null,
        public ?string $method = null,
        public ?string $property = null,
        public ?string $parameter = null,
        public ?string $constant = null,
    ) {
    }

    public static function fromReflector(\Reflector $reflector): self
    {
        $class = null;
        $method = null;
        $property = null;
        $parameter = null;
        $constant = null;
        $filename = null;
        $line = null;
        $namespace = null;

        if ($reflector instanceof \ReflectionClass) {
            $class = $reflector->getName();
            $namespace = $reflector->getNamespaceName() ?: null;
            $filename = $reflector->getFileName() ?: null;
            $line = $reflector->getStartLine() ?: null;
        } elseif ($reflector instanceof \ReflectionMethod) {
            $class = $reflector->getDeclaringClass()->getName();
            $method = $reflector->getName();
            $namespace = $reflector->getDeclaringClass()->getNamespaceName() ?: null;
            $filename = $reflector->getFileName() ?: null;
            $line = $reflector->getStartLine() ?: null;
        } elseif ($reflector instanceof \ReflectionProperty) {
            $class = $reflector->getDeclaringClass()->getName();
            $property = $reflector->getName();
            $namespace = $reflector->getDeclaringClass()->getNamespaceName() ?: null;
            $filename = $reflector->getDeclaringClass()->getFileName() ?: null;
            $line = null;
        } elseif ($reflector instanceof \ReflectionParameter) {
            $function = $reflector->getDeclaringFunction();
            $parameter = $reflector->getName();
            if ($function instanceof \ReflectionMethod) {
                $class = $function->getDeclaringClass()->getName();
                $method = $function->getName();
                $namespace = $function->getDeclaringClass()->getNamespaceName() ?: null;
            }
            $filename = $function->getFileName() ?: null;
            $line = $function->getStartLine() ?: null;
        } elseif ($reflector instanceof \ReflectionClassConstant) {
            $class = $reflector->getDeclaringClass()->getName();
            $constant = $reflector->getName();
            $namespace = $reflector->getDeclaringClass()->getNamespaceName() ?: null;
            $filename = $reflector->getDeclaringClass()->getFileName() ?: null;
            $line = null;
        }

        return new self(
            filename: $filename,
            line: $line,
            namespace: $namespace,
            class: $class,
            method: $method,
            property: $property,
            parameter: $parameter,
            constant: $constant,
        );
    }

    public function __toString(): string
    {
        $parts = [];

        if ($this->class !== null) {
            $parts[] = $this->class;
        }

        if ($this->method !== null) {
            $parts[] = ($parts !== [] ? '::' : '') . $this->method . '()';
        }

        if ($this->property !== null) {
            $parts[] = ($parts !== [] ? '::$' : '$') . $this->property;
        }

        if ($this->parameter !== null) {
            $parts[] = ($parts !== [] ? ' $' : '$') . $this->parameter;
        }

        if ($this->constant !== null) {
            $parts[] = ($parts !== [] ? '::' : '') . $this->constant;
        }

        $location = implode('', $parts);

        if ($this->filename !== null) {
            $file = $this->filename;
            if ($this->line !== null) {
                $file .= ':' . $this->line;
            }
            $location = $location ? "{$location} in {$file}" : $file;
        }

        return $location ?: 'unknown';
    }
}
