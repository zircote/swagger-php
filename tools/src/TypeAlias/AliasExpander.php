<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\TypeAlias;

/**
 * Reads `@phpstan-type` aliases out of a docblock and substitutes them into a type.
 *
 * Aliases are a static-analysis convention: nothing at runtime resolves them, so anything that
 * reads this project's own docblocks has to expand them itself. The doc generator does, because
 * it renders types verbatim into the published reference; the tests that resolve declared types
 * do, because `symfony/type-info` rejects a name it cannot resolve.
 *
 * Dev-only by design. It lives under `tools/` rather than `src/` because `composer.json` ships
 * `OpenApi\ => src` and nothing else, and no shipped code has any use for it.
 */
final class AliasExpander
{
    /**
     * Extract alias declarations from a docblock.
     *
     * @return array{aliases: array<string, string>, imports: array<string, array{name: string, from: string}>}
     */
    public static function parse(string|false|null $docblock): array
    {
        $aliases = [];
        $imports = [];

        if (!$docblock) {
            return ['aliases' => $aliases, 'imports' => $imports];
        }

        foreach (preg_split('/(\n|\r\n)/', $docblock) ?: [] as $line) {
            $line = trim((string) preg_replace('/^\s*(?:\/\*\*|\*)\s?/', '', $line));

            // `@phpstan-type Name <definition>`; the definition runs to end of line.
            if (preg_match('/^@phpstan-type\s+(\w+)\s+(.+?)\s*(?:\*\/)?$/', $line, $match) === 1) {
                $aliases[$match[1]] = trim($match[2]);

                continue;
            }

            // `@phpstan-import-type Name from Source [as Local]`.
            if (preg_match('/^@phpstan-import-type\s+(\w+)\s+from\s+([\w\\\\]+)(?:\s+as\s+(\w+))?/', $line, $match) === 1) {
                $imports[($match[3] ?? '') ?: $match[1]] = ['name' => $match[1], 'from' => $match[2]];
            }
        }

        return ['aliases' => $aliases, 'imports' => $imports];
    }

    /**
     * Every alias in scope for a class: its own, plus the ones it imports.
     *
     * An import is followed one hop. Chaining is possible but nothing here needs it, and a
     * single hop cannot loop.
     *
     * @param \ReflectionClass<object> $rc
     *
     * @return array<string, string>
     */
    public static function aliasesFor(\ReflectionClass $rc): array
    {
        $parsed = self::parse($rc->getDocComment());
        $aliases = $parsed['aliases'];

        // A docblock inherited from a trait or a parent carries that scope's aliases with it:
        // `ReflectionProperty::getDeclaringClass()` names the using class, while the `@var`
        // being expanded — and the import that explains it — live in the trait. Nearest scope
        // wins, so a class can still override what it inherits.
        foreach ([...array_values($rc->getTraits()), ...($rc->getParentClass() ? [$rc->getParentClass()] : [])] as $inherited) {
            $aliases += self::aliasesFor($inherited);
        }

        foreach ($parsed['imports'] as $local => $import) {
            if (null !== $source = self::resolveSource($import['from'], $rc)) {
                $imported = self::parse($source->getDocComment())['aliases'];
                if (isset($imported[$import['name']])) {
                    $aliases[$local] = $imported[$import['name']];
                }
            }
        }

        return $aliases;
    }

    /**
     * Substitute a class's aliases into a type.
     *
     * An unknown name is left alone: a type that merely looks like an alias is far more likely
     * to be a class name.
     *
     * @param \ReflectionClass<object> $rc
     */
    public static function expand(string $type, \ReflectionClass $rc): string
    {
        $aliases = self::aliasesFor($rc);

        if ([] === $aliases) {
            return $type;
        }

        // Longest first, so `EnumValues` is not half-replaced by an alias named `Enum`.
        uksort($aliases, static fn (string $a, string $b): int => strlen($b) <=> strlen($a));

        foreach ($aliases as $name => $definition) {
            $type = (string) preg_replace('/(?<![\\\\\w])' . preg_quote($name, '/') . '(?![\w])/', $definition, $type);
        }

        return $type;
    }

    /**
     * Resolve an import's source: as written, then relative to the importing class's namespace.
     *
     * @param \ReflectionClass<object> $rc
     *
     * @return \ReflectionClass<object>|null
     */
    protected static function resolveSource(string $from, \ReflectionClass $rc): ?\ReflectionClass
    {
        foreach ([ltrim($from, '\\'), $rc->getNamespaceName() . '\\' . ltrim($from, '\\')] as $candidate) {
            if (class_exists($candidate) || interface_exists($candidate) || trait_exists($candidate)) {
                return new \ReflectionClass($candidate);
            }
        }

        return null;
    }
}
