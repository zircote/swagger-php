<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Rector;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\UseItem;
use PhpParser\NodeFinder;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Collapses fully qualified `OpenApi\Spec` references onto a namespace alias, and drops the
 * classic import they replaced.
 *
 * Rector renames classes to fully qualified names and has no concept of a namespace alias, so
 * without this a migrated file reads `#[\OpenApi\Spec\Schema]` with a stranded
 * `use OpenApi\Attributes as OAT;` above it.
 *
 * The alias defaults to `OAS` rather than the `OA` the documentation teaches, on purpose:
 *
 * - it cannot collide, where `OA` competes with the `use OpenApi\Annotations as OA;` that
 *   hybrid files already carry;
 * - it marks the file as migrated. `grep -rl 'as OAS'` answers "how far did I get", which
 *   matters for a migration run in stages, and stays true through the 8.0 namespace move,
 *   where `OpenApi\Spec` becomes `OpenApi\Attributes` and a migrated import would otherwise
 *   be indistinguishable from an untouched classic one;
 * - finishing is `s/OAS\\/OA\\/` plus the import line, and `OAS` is distinctive enough to
 *   do that with sed, which `OA` never would be.
 *
 * Set the alias to `OA` if you would rather land on the documented convention directly and
 * have no hybrid files.
 */
final class AliasSpecImportsRector extends AbstractRector implements ConfigurableRectorInterface
{
    public const ALIAS = 'alias';

    private const SPEC_NAMESPACE = 'OpenApi\Spec';

    private const CLASSIC_NAMESPACE = 'OpenApi\Attributes';

    private string $alias = 'OAS';

    public function configure(array $configuration): void
    {
        $this->alias = $configuration[self::ALIAS] ?? 'OAS';
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Alias fully qualified OpenApi\Spec references and drop the classic import', [new ConfiguredCodeSample(
            <<<'CODE_SAMPLE'
use OpenApi\Attributes as OAT;

#[\OpenApi\Spec\Schema(component: 'Pet')]
class Pet {}
CODE_SAMPLE,
            <<<'CODE_SAMPLE'
use OpenApi\Spec as OAS;

#[OAS\Schema(component: 'Pet')]
class Pet {}
CODE_SAMPLE,
            [self::ALIAS => 'OAS']
        )]);
    }

    public function getNodeTypes(): array
    {
        return [Name::class, Namespace_::class];
    }

    public function refactor(Node $node): ?Node
    {
        if ($node instanceof Name) {
            return $this->aliasSpecName($node);
        }

        if (!$node instanceof Namespace_) {
            return null;
        }

        $removed = $this->removeClassicImports($node);

        // A namespace is visited before its children, so on the pass that drops the classic
        // import the references below are still classic: nothing spec-shaped is visible yet.
        // Dropping the import and adding the alias are therefore one decision, not two.
        $added = ($removed || $this->needsAlias($node)) && $this->ensureAliasImport($node);

        return ($removed || $added) ? $node : null;
    }

    /**
     * `\OpenApi\Spec\Foo\Bar` becomes `<alias>\Foo\Bar`.
     *
     * Returning the replacement rather than mutating in place is what makes Rector reprint it;
     * a rule that rewrites grandchildren through its own traverser is silently ignored by the
     * printer, which only knows about nodes a rule handed back.
     */
    private function aliasSpecName(Name $name): ?Name
    {
        $prefix = self::SPEC_NAMESPACE . '\\';
        $full = $name->toString();

        if (!str_starts_with($full, $prefix)) {
            return null;
        }

        // Name resolution rewrites `OAS\Schema` to its fully qualified form before a rule sees
        // it, so an already-aliased reference looks exactly like one that still needs work.
        // Without this the rule reports a change on every pass over a migrated file and prints
        // the same text back, which Rector flags as a rule applied to no effect.
        $original = $name->getAttribute('originalName');
        if ($original instanceof Name && $original->getFirst() === $this->alias) {
            return null;
        }

        return new Name($this->alias . '\\' . substr($full, strlen($prefix)));
    }

    /**
     * Drops the imports the alias replaces: classic `OpenApi\Attributes...`, which nothing
     * references once the classes are renamed, and any existing `OpenApi\Spec` import -- a
     * per-class `use OpenApi\Spec\Schema;` or a bare `use OpenApi\Spec;`.
     *
     * Clearing the spec ones matters beyond tidiness. A namespace is visited before its
     * children, so once they are gone the only spec name left in a `use` is `OpenApi\Spec`
     * itself, which the `OpenApi\Spec\` prefix check excludes -- and the name rewriter below
     * can no longer corrupt an import into `use OAS\Schema;`.
     */
    private function removeClassicImports(Namespace_ $namespace): bool
    {
        $changed = false;

        foreach ($namespace->stmts as $key => $stmt) {
            if (!$stmt instanceof Use_) {
                continue;
            }

            $keep = [];
            foreach ($stmt->uses as $use) {
                $name = $use->name->toString();

                // the alias import itself survives, or the rule would churn on every pass
                if ($name === self::SPEC_NAMESPACE && $use->alias?->toString() === $this->alias) {
                    $keep[] = $use;

                    continue;
                }

                if ($this->isReplacedImport($name)) {
                    $changed = true;

                    continue;
                }
                $keep[] = $use;
            }

            if (!$keep) {
                unset($namespace->stmts[$key]);

                continue;
            }
            $stmt->uses = $keep;
        }

        if ($changed) {
            $namespace->stmts = array_values($namespace->stmts);
        }

        return $changed;
    }

    /**
     * True for an import the alias replaces: the classic namespace or a class in it, and any
     * spec import other than the alias itself.
     */
    private function isReplacedImport(string $name): bool
    {
        foreach ([self::CLASSIC_NAMESPACE, self::SPEC_NAMESPACE] as $namespace) {
            if ($name === $namespace || str_starts_with($name, $namespace . '\\')) {
                return true;
            }
        }

        return false;
    }

    private function ensureAliasImport(Namespace_ $namespace): bool
    {

        foreach ($namespace->stmts as $stmt) {
            if (!$stmt instanceof Use_) {
                continue;
            }
            foreach ($stmt->uses as $use) {
                if ($use->name->toString() === self::SPEC_NAMESPACE) {
                    return false;
                }
            }
        }

        $use = new Use_([new UseItem(new Name(self::SPEC_NAMESPACE), $this->alias)]);

        $at = 0;
        foreach ($namespace->stmts as $key => $stmt) {
            if ($stmt instanceof Use_) {
                $at = $key + 1;
            }
        }

        array_splice($namespace->stmts, $at, 0, [$use]);

        return true;
    }

    /**
     * True when the file references the spec namespace, whether or not the references have been
     * rewritten yet. A namespace is visited before its children, so on the pass that rewrites
     * them this sees the fully qualified form; on any later pass it sees the alias. Checking
     * both is what makes the import independent of visit order.
     */
    private function needsAlias(Namespace_ $namespace): bool
    {
        $prefix = self::SPEC_NAMESPACE . '\\';

        foreach ((new NodeFinder())->findInstanceOf($namespace->stmts, Name::class) as $name) {
            if ($name->getFirst() === $this->alias || str_starts_with($name->toString(), $prefix)) {
                return true;
            }
        }

        return false;
    }
}
