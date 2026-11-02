<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Specification;

use OpenApi\OpenApiException;

/**
 * Which component a use means: a class, plus the qualifiers that tell its descriptions apart.
 *
 * A key without qualifiers is plain, and is what a class name written as a `$ref` or a property
 * typed with a class looks up. A class can be described more than once, such as once per
 * serialization group, and each further description adds qualifiers to tell it apart.
 * The key is not the component's name: two keys can be told apart before either has a name.
 *
 * Qualifiers are one level deep, a name mapped to a scalar or a list of scalars. Names are sorted,
 * and so is each list, so two keys built in a different order are the same key and a list acts as
 * a set. A value whose order matters goes in as one string.
 */
final readonly class ComponentKey implements \Stringable
{
    /**
     * @var array<string, scalar|list<scalar>>
     */
    public array $qualifiers;

    /**
     * @param array<string, mixed> $qualifiers a scalar or a list of scalars each, checked here
     */
    public function __construct(
        public string $class,
        array $qualifiers = [],
    ) {
        foreach ($qualifiers as $name => $value) {
            if (is_array($value)) {
                if (!array_is_list($value) || array_filter($value, fn (mixed $item): bool => !is_scalar($item)) !== []) {
                    throw new OpenApiException("Component key qualifier '{$name}' of {$class} must be a scalar or a list of scalars");
                }

                sort($value);
                $qualifiers[$name] = $value;
            } elseif (!is_scalar($value)) {
                throw new OpenApiException("Component key qualifier '{$name}' of {$class} must be a scalar or a list of scalars");
            }
        }

        ksort($qualifiers);
        $this->qualifiers = $qualifiers;
    }

    /**
     * The class, followed by the qualifiers as JSON when there are any. Equal keys give equal strings.
     */
    public function __toString(): string
    {
        return $this->qualifiers === []
            ? $this->class
            : $this->class . json_encode($this->qualifiers, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }

    /**
     * Whether the key is the class alone.
     */
    public function isPlain(): bool
    {
        return $this->qualifiers === [];
    }

    public function equals(self $other): bool
    {
        return (string) $this === (string) $other;
    }
}
