<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Rector;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Rewrites a parameter's `spaceDelimited:`, `pipeDelimited:` or `deepObject:` to `style:`.
 *
 * Classic takes the three as arguments of their own and writes each into the document under its
 * own name, a key OpenAPI does not define. The spec attributes take only `style:`, which is what
 * the arguments stand for, so the migrated document gains the style and loses the stray key.
 *
 * Left alone where `style:` is already given, where more than one of the three is, and where the
 * value is `false` or `null`. Spec's header and cookie parameters take no `style:`, so they are
 * not matched. Both names are matched: on the pass that renames the class this rule may see
 * either.
 */
final class ParameterStyleRector extends AbstractRector
{
    private const CLASSES = [
        'OpenApi\Attributes\Parameter',
        'OpenApi\Attributes\QueryParameter',
        'OpenApi\Attributes\PathParameter',
        'OpenApi\Spec\Parameter',
        'OpenApi\Spec\Parameter\Query',
        'OpenApi\Spec\Parameter\Path',
    ];

    private const STYLES = ['spaceDelimited', 'pipeDelimited', 'deepObject'];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Rewrite a parameter\'s spaceDelimited:, pipeDelimited: or deepObject: to style:', [new CodeSample(
            <<<'CODE_SAMPLE'
#[\OpenApi\Attributes\QueryParameter(name: 'filter', deepObject: true)]
CODE_SAMPLE,
            <<<'CODE_SAMPLE'
#[\OpenApi\Attributes\QueryParameter(name: 'filter', style: 'deepObject')]
CODE_SAMPLE
        )]);
    }

    public function getNodeTypes(): array
    {
        return [Attribute::class, New_::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (!$node instanceof Attribute && !$node instanceof New_) {
            return null;
        }

        $class = $node instanceof Attribute ? $node->name : $node->class;
        if (!$class instanceof Node\Name || !in_array($this->getName($class), self::CLASSES, true)) {
            return null;
        }

        $style = null;
        foreach ($node->args as $arg) {
            if (!$arg instanceof Arg || !$arg->name instanceof Identifier) {
                continue;
            }

            $name = $arg->name->toString();
            if ('style' === $name) {
                return null;
            }

            if (in_array($name, self::STYLES, true)) {
                if (null !== $style || $this->isUnset($arg->value)) {
                    return null;
                }

                $style = $arg;
            }
        }

        if (null === $style) {
            return null;
        }

        $style->value = new String_($style->name->toString());
        $style->name = new Identifier('style');

        return $node;
    }

    private function isUnset(Node\Expr $value): bool
    {
        return $value instanceof ConstFetch && ($this->isName($value, 'false') || $this->isName($value, 'null'));
    }
}
