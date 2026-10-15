<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Rector\TypeAliasesRector;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Type\TypeMapper;
use OpenApi\Utils\PipeInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Translates the type names classic accepts in an explicit `type` into OpenAPI types.
 *
 * Classic maps `int`, `bool`, `long`, `float`, `double`, `date` and the date-time class names
 * through {@see TypeMapper::NATIVE_TYPE_MAP}, so `type: 'long'` becomes `integer` with format
 * `int64`. The format is set only where none is given, and in a type list each name gets its
 * type alone, as in classic. Each translation logs a deprecation. `mixed` and `byte` are left
 * as they are, and the compiler reports them as unknown types.
 *
 * Runs first, so every augmenter after it sees valid types, and a schema that `Inheritance`
 * copies is translated, and reported, once.
 *
 * @deprecated since 6.15, removed in 8.0 - write the OpenAPI type; {@see TypeAliasesRector} rewrites it
 *
 * @implements PipeInterface<Specification>
 */
class TypeAliases implements PipeInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected const UNMAPPED = ['mixed', 'byte'];

    /**
     * A schema nested in a `schema:` argument has no reflector of its own, so a report about it
     * names the attribute holding it.
     *
     * @var \SplObjectStorage<OA\Schema, AttributeInterface>
     */
    protected \SplObjectStorage $holders;

    public function __construct(
        protected bool $enabled = true,
    ) {
    }

    public function __invoke(mixed $payload): mixed
    {
        if (!$this->enabled) {
            return null;
        }

        $this->holders = new \SplObjectStorage();
        $payload->getWalker()->visit(AttributeInterface::class, function (AttributeInterface $attribute): void {
            if (property_exists($attribute, 'schema') && $attribute->schema instanceof OA\Schema) {
                $this->holders[$attribute->schema] = $attribute;
            }
        });

        $payload->getWalker()->eachSchema(function (OA\Schema $schema): void {
            if (is_string($schema->type)) {
                $this->translate($schema);
            } elseif (is_array($schema->type)) {
                $this->translateList($schema);
            }
        });

        return null;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function group(): string|\BackedEnum
    {
        return Group::Resolve;
    }

    protected function translate(OA\Schema $schema): void
    {
        /** @var string $type */
        $type = $schema->type;
        $mapped = $this->lookup($type);
        if ($mapped === null) {
            return;
        }

        [$target, $format] = is_array($mapped) ? $mapped : [$mapped, null];
        if ($target === $type) {
            return;
        }

        $schema->type = $target;
        if ($format !== null) {
            $schema->format ??= $format;
        }

        $this->report($schema, $type, $format === null ? "\"$target\"" : "\"$target\" with format \"$format\"");
    }

    protected function translateList(OA\Schema $schema): void
    {
        /** @var list<string> $types */
        $types = $schema->type;

        foreach ($types as $index => $type) {
            $mapped = $this->lookup($type);
            if ($mapped === null) {
                continue;
            }

            $target = is_array($mapped) ? $mapped[0] : $mapped;
            if ($target === $type) {
                continue;
            }

            $types[$index] = $target;
            $this->report($schema, $type, "\"$target\"");
        }

        $schema->type = $types;
    }

    /**
     * @return string|array{string, string}|null
     */
    protected function lookup(string $type): string|array|null
    {
        $key = strtolower($type);

        if (in_array($key, static::UNMAPPED, true) || !array_key_exists($key, TypeMapper::NATIVE_TYPE_MAP)) {
            return null;
        }

        return TypeMapper::NATIVE_TYPE_MAP[$key];
    }

    protected function report(OA\Schema $schema, string $alias, string $replacement): void
    {
        $located = !$schema->getReflector() instanceof \Reflector && isset($this->holders[$schema]) ? $this->holders[$schema] : $schema;

        $this->logger?->warning(sprintf(
            'Schema type "%s" is a classic alias, use %s (translation removed in 8.0; rector rule %s rewrites it) in %s',
            $alias,
            $replacement,
            TypeAliasesRector::class,
            $located->getSourceLocation(),
        ));
    }
}
