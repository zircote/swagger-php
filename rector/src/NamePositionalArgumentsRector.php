<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Rector;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeVisitor;
use PHPStan\Reflection\ReflectionProvider;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Names positional arguments to a classic attribute by the classic signature, before the rename.
 *
 * The spec classes order their parameters differently: classic `Response` takes `ref, response,
 * description`, spec `Response` takes `response, description, ref`. A positional call survives
 * the class rename unchanged and binds every argument to the wrong parameter. Where the types
 * clash, that fails at load; where they do not -- two `?array`s swapping places -- it emits a
 * wrong document without a word. Named, an argument either lands where it was meant to or is
 * rejected as unknown, which is a failure that says where it is.
 *
 * Three shapes: an attribute, a `new` expression, and `parent::__construct()` in a class whose
 * parent constructor is a classic attribute's -- directly, or inherited through user classes
 * that declare none of their own. The last is the easiest to miss: a project that subclasses
 * `Response` for its stock responses calls the parent positionally once, and every response
 * built from that class goes through the call.
 *
 * The classic name is matched because a parent node is visited before its children, so this
 * runs while the name is still classic -- which is the only moment the classic signature is
 * still the right one to read.
 */
final class NamePositionalArgumentsRector extends AbstractRector
{
    private const CLASSIC = 'OpenApi\\Attributes\\';

    /** @var array<string, list<string>> */
    private array $parameters = [];

    public function __construct(
        private readonly ReflectionProvider $reflectionProvider,
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Name positional arguments to a classic attribute by its classic signature', [new CodeSample(
            <<<'CODE_SAMPLE'
class NotFound extends \OpenApi\Attributes\Response
{
    public function __construct()
    {
        parent::__construct(null, 404, 'Not found');
    }
}
CODE_SAMPLE,
            <<<'CODE_SAMPLE'
class NotFound extends \OpenApi\Attributes\Response
{
    public function __construct()
    {
        parent::__construct(ref: null, response: 404, description: 'Not found');
    }
}
CODE_SAMPLE
        )]);
    }

    public function getNodeTypes(): array
    {
        return [Attribute::class, New_::class, Class_::class];
    }

    public function refactor(Node $node): ?Node
    {
        if ($node instanceof Class_) {
            return $this->refactorSubclass($node);
        }

        if (!$node instanceof Attribute && !$node instanceof New_) {
            return null;
        }

        // an attribute names its class in `name`, a `new` expression in `class`
        $class = $node instanceof Attribute ? $node->name : $node->class;
        if (!$class instanceof Node\Name) {
            return null;
        }

        $name = $this->getName($class);
        if (null === $name || !str_starts_with($name, self::CLASSIC)) {
            return null;
        }

        return $this->nameArguments($node->args, $name) ? $node : null;
    }

    private function refactorSubclass(Class_ $class): ?Class_
    {
        if (!$class->extends instanceof Node\Name) {
            return null;
        }

        $parent = $this->constructorOwner($this->getName($class->extends));
        if (null === $parent || !str_starts_with($parent, self::CLASSIC)) {
            return null;
        }

        $changed = false;
        $this->traverseNodesWithCallable($class->stmts, function (Node $node) use ($parent, &$changed): ?int {
            // a nested class has a parent of its own
            if ($node instanceof Class_) {
                return NodeVisitor::DONT_TRAVERSE_CHILDREN;
            }

            if ($node instanceof StaticCall
                && $node->class instanceof Node\Name
                && 'parent' === $node->class->toLowerString()
                && $this->isName($node->name, '__construct')
                && $this->nameArguments($node->args, $parent)) {
                $changed = true;
            }

            return null;
        });

        return $changed ? $class : null;
    }

    /**
     * The class whose constructor `parent::__construct()` calls: the parent's own, or the one it
     * inherits. A user class between the subclass and the attribute only matters if it declares
     * a constructor, since then its signature is the one being called.
     */
    private function constructorOwner(?string $parent): ?string
    {
        if (null === $parent || str_starts_with($parent, self::CLASSIC)) {
            return $parent;
        }

        if (!$this->reflectionProvider->hasClass($parent)) {
            return null;
        }

        $reflection = $this->reflectionProvider->getClass($parent);

        return $reflection->hasConstructor()
            ? $reflection->getConstructor()->getDeclaringClass()->getName()
            : null;
    }

    /**
     * Names every positional argument, or none: a call that cannot be read in full is left as
     * the author wrote it rather than half-converted.
     *
     * @param array<Arg|Node\VariadicPlaceholder> $args
     */
    private function nameArguments(array $args, string $class): bool
    {
        $parameters = $this->parameters($class);

        $positional = [];
        foreach ($args as $index => $arg) {
            if (!$arg instanceof Arg || $arg->unpack) {
                return false;
            }

            // positional arguments cannot follow named ones, so the first name ends the run
            if ($arg->name instanceof Identifier) {
                break;
            }

            if (!isset($parameters[$index])) {
                return false;
            }

            $positional[$index] = $arg;
        }

        foreach ($positional as $index => $arg) {
            $arg->name = new Identifier($parameters[$index]);
        }

        return [] !== $positional;
    }

    /**
     * @return list<string>
     */
    private function parameters(string $class): array
    {
        if (isset($this->parameters[$class])) {
            return $this->parameters[$class];
        }

        if (!class_exists($class)) {
            return $this->parameters[$class] = [];
        }

        $constructor = (new \ReflectionClass($class))->getConstructor();

        return $this->parameters[$class] = $constructor
            ? array_map(static fn (\ReflectionParameter $p): string => $p->getName(), $constructor->getParameters())
            : [];
    }
}
