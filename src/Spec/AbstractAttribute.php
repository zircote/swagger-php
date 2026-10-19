<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Utils\SourceLocation;

abstract class AbstractAttribute implements AttributeInterface
{
    protected ?\Reflector $reflector = null;

    protected ?SourceLocation $sourceLocation = null;

    /**
     * @var array<string,mixed>
     */
    protected array $meta = [];

    protected ?Fan $fan = null;

    /**
     * @param array<string,mixed>|null $x
     * @param list<Attachable>|null    $attachables Reusable custom attachable attributes
     * @param Fan|null                 $fan         Where this attribute goes when more than one attribute could hold it
     */
    public function __construct(
        public ?array $x = null,
        public ?array $attachables = null,
        ?Fan $fan = null,
    ) {
        $this->fan = $fan;
    }

    public function isRoot(): bool
    {
        return false;
    }

    public function merge(): array
    {
        return [];
    }

    public function contained(): array
    {
        return [];
    }

    public function getReflector(): ?\Reflector
    {
        return $this->reflector;
    }

    /**
     * @return \ReflectionClass<object>|null
     */
    public function getClassReflector(): ?\ReflectionClass
    {
        $reflector = $this->reflector;

        if ($reflector instanceof \ReflectionClass) {
            return $reflector;
        }

        if ($reflector instanceof \ReflectionMethod || $reflector instanceof \ReflectionProperty || $reflector instanceof \ReflectionClassConstant) {
            return $reflector->getDeclaringClass();
        }

        if ($reflector instanceof \ReflectionParameter) {
            $function = $reflector->getDeclaringFunction();

            return $function instanceof \ReflectionMethod ? $function->getDeclaringClass() : null;
        }

        return null;
    }

    /**
     * @return class-string|null
     */
    public function getClassName(): ?string
    {
        return $this->getClassReflector()?->getName();
    }

    public function getShortClassName(): ?string
    {
        return $this->getClassReflector()?->getShortName();
    }

    public function setReflector(?\Reflector $reflector): static
    {
        $this->reflector = $reflector;
        $this->sourceLocation = null;

        return $this;
    }

    public function getFan(): ?Fan
    {
        return $this->fan;
    }

    public function setFan(?Fan $fan): static
    {
        $this->fan = $fan;

        return $this;
    }

    public function copy(): static
    {
        $copy = clone $this;

        foreach (get_object_vars($copy) as $property => $value) {
            $copy->{$property} = self::copyValue($value);
        }

        return $copy;
    }

    public function getMeta(string $key, mixed $default = null): mixed
    {
        return array_key_exists($key, $this->meta) ? $this->meta[$key] : $default;
    }

    public function setMeta(string $key, mixed $value): static
    {
        $this->meta[$key] = $value;

        return $this;
    }

    public function getSourceLocation(): SourceLocation
    {
        if (!$this->sourceLocation instanceof SourceLocation) {
            $this->sourceLocation = $this->reflector instanceof \Reflector
                ? SourceLocation::fromReflector($this->reflector)
                : new SourceLocation();
        }

        return $this->sourceLocation;
    }

    /**
     * @template T
     *
     * @param T|list<T>|null $value
     *
     * @return list<T>|null
     */
    protected static function wrapList(mixed $value): ?array
    {
        if ($value === null) {
            return null;
        }

        if (!is_array($value)) {
            return [$value];
        }

        /** @var list<T> $list array_values() guarantees the list shape the return type declares */
        $list = array_values($value);

        return $list;
    }

    /**
     * A bare schema stands for one media type with that payload and no `mediaType` yet.
     *
     * @param MediaType|list<MediaType>|Schema|null $content
     *
     * @return list<MediaType>|null
     */
    protected static function wrapContent(MediaType|Schema|array|null $content): ?array
    {
        return $content instanceof Schema ? [new MediaType(schema: $content)] : self::wrapList($content);
    }

    private static function copyValue(mixed $value): mixed
    {
        if ($value instanceof AttributeInterface) {
            return $value->copy();
        }

        return is_array($value) ? array_map(self::copyValue(...), $value) : $value;
    }
}
