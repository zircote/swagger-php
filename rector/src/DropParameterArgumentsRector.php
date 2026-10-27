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
use PhpParser\Node\Scalar\String_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Drops parameter and header arguments the spec attributes do not take, where nothing is lost.
 *
 * Two kinds. `in:` on a parameter subclass repeats what the class already says, and is dropped
 * only where its value matches. The rest are keys OpenAPI does not define in that place:
 * `allowEmptyValue` and `allowReserved` outside query parameters, and any style on a header or
 * cookie parameter, which allows one each. Classic writes them into the document as given; the
 * migrated document leaves them out.
 *
 * Both names are matched: on the pass that renames the class this rule may see either.
 */
final class DropParameterArgumentsRector extends AbstractRector
{
    /**
     * The arguments to drop per class: a name with the value `in:` must have, or with null for
     * any value.
     *
     * @var array<string, array<string, string|null>>
     */
    private const DROP = [
        'OpenApi\Attributes\QueryParameter' => ['in' => 'query'],
        'OpenApi\Attributes\PathParameter' => ['in' => 'path', 'allowEmptyValue' => null, 'allowReserved' => null],
        'OpenApi\Attributes\HeaderParameter' => ['in' => 'header', 'allowEmptyValue' => null, 'allowReserved' => null, 'style' => null, 'spaceDelimited' => null, 'pipeDelimited' => null, 'deepObject' => null],
        'OpenApi\Attributes\CookieParameter' => ['in' => 'cookie', 'allowEmptyValue' => null, 'allowReserved' => null, 'style' => null, 'spaceDelimited' => null, 'pipeDelimited' => null, 'deepObject' => null],
        'OpenApi\Attributes\Header' => ['allowEmptyValue' => null],
    ];

    /** Spec names, valued by the classic name whose list applies. */
    private const SPEC = [
        'OpenApi\Spec\Parameter\Query' => 'OpenApi\Attributes\QueryParameter',
        'OpenApi\Spec\Parameter\Path' => 'OpenApi\Attributes\PathParameter',
        'OpenApi\Spec\Parameter\Header' => 'OpenApi\Attributes\HeaderParameter',
        'OpenApi\Spec\Parameter\Cookie' => 'OpenApi\Attributes\CookieParameter',
        'OpenApi\Spec\Header' => 'OpenApi\Attributes\Header',
    ];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Drop parameter and header arguments the spec attributes do not take, where nothing is lost', [new CodeSample(
            <<<'CODE_SAMPLE'
#[\OpenApi\Attributes\HeaderParameter(name: 'X-Id', in: 'header', style: 'simple', allowEmptyValue: true)]
CODE_SAMPLE,
            <<<'CODE_SAMPLE'
#[\OpenApi\Attributes\HeaderParameter(name: 'X-Id')]
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
        $name = $class instanceof Node\Name ? $this->getName($class) : null;
        $drop = self::DROP[self::SPEC[$name] ?? $name] ?? null;
        if (null === $drop) {
            return null;
        }

        $args = [];
        foreach ($node->args as $arg) {
            if ($arg instanceof Arg && $arg->name instanceof Identifier && array_key_exists($arg->name->toString(), $drop)) {
                $value = $drop[$arg->name->toString()];
                if (null === $value || ($arg->value instanceof String_ && $arg->value->value === $value)) {
                    continue;
                }
            }

            $args[] = $arg;
        }

        if (count($args) === count($node->args)) {
            return null;
        }

        $node->args = $args;

        return $node;
    }
}
