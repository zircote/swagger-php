<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Rector;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name\FullyQualified;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Turns a `Property` with `encoding:` into a `Property\Encoded`.
 *
 * Spec `Property` takes no `encoding:`; the variant that does is `Property\Encoded`. Only the
 * class changes here: the schema keywords beside it are moved by
 * {@see MoveSchemaKeywordsRector}, which reads what `Property\Encoded` accepts and so keeps
 * `encoding:` where it is.
 *
 * Both names are matched: on the pass that renames the class this rule may see either.
 */
final class EncodedPropertyRector extends AbstractRector
{
    private const CLASSES = ['OpenApi\Attributes\Property', 'OpenApi\Spec\Property'];

    private const ENCODED = 'OpenApi\Spec\Property\Encoded';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Turn a Property with encoding: into a Property\Encoded', [new CodeSample(
            <<<'CODE_SAMPLE'
#[\OpenApi\Attributes\Property(type: 'string', encoding: new \OpenApi\Attributes\Encoding(contentType: 'image/png'))]
CODE_SAMPLE,
            <<<'CODE_SAMPLE'
#[\OpenApi\Spec\Property\Encoded(type: 'string', encoding: new \OpenApi\Attributes\Encoding(contentType: 'image/png'))]
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

        foreach ($node->args as $arg) {
            if ($arg instanceof Arg && $this->isName($arg, 'encoding')) {
                if ($node instanceof Attribute) {
                    $node->name = new FullyQualified(self::ENCODED);
                } else {
                    $node->class = new FullyQualified(self::ENCODED);
                }

                return $node;
            }
        }

        return null;
    }
}
