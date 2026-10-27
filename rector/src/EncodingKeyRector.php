<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Rector;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Identifier;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Renames `Encoding`'s `property:` to `encoding:`.
 *
 * Classic `Encoding` names the property it applies to `property:`; the spec one calls it
 * `encoding:` and has no `property:` at all, so a renamed class with the classic argument
 * fails on load with `Unknown named parameter $property`.
 *
 * Both names are matched: on the pass that renames the class this rule may see either.
 */
final class EncodingKeyRector extends AbstractRector
{
    private const CLASSES = ['OpenApi\Attributes\Encoding', 'OpenApi\Spec\Encoding'];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Rename Encoding\'s property: to encoding:', [new CodeSample(
            <<<'CODE_SAMPLE'
new \OpenApi\Attributes\Encoding(property: 'file', contentType: 'image/png')
CODE_SAMPLE,
            <<<'CODE_SAMPLE'
new \OpenApi\Attributes\Encoding(encoding: 'file', contentType: 'image/png')
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

        $key = null;
        foreach ($node->args as $arg) {
            if (!$arg instanceof Arg) {
                continue;
            }

            // both given leaves two values to choose from
            if ($this->isName($arg, 'encoding')) {
                return null;
            }

            if ($this->isName($arg, 'property')) {
                $key = $arg;
            }
        }

        if (null === $key) {
            return null;
        }

        $key->name = new Identifier('encoding');

        return $node;
    }
}
