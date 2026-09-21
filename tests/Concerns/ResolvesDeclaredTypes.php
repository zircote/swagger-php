<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Concerns;

use OpenApi\Tools\TypeAlias\AliasExpander;
use Symfony\Component\TypeInfo\Type;
use Symfony\Component\TypeInfo\TypeContext\TypeContextFactory;
use Symfony\Component\TypeInfo\TypeResolver\TypeResolver;

/**
 * Resolves a declared property type, seeing through `@phpstan-type` aliases.
 *
 * `symfony/type-info` reads the docblock itself and knows nothing about aliases, so a property
 * declared as `list<EnumValue>` makes it throw rather than resolve. Expanding first is what
 * lets this project declare a shared union once instead of spelling it out at every site.
 *
 * Deliberately not a `try`/`catch` around the resolver: an unresolvable type is a real finding
 * — a malformed docblock the suite should fail on — and swallowing it here would cost exactly
 * the check these tests exist to perform. Only a name the class actually declares as an alias
 * is substituted; everything else reaches the resolver untouched.
 */
trait ResolvesDeclaredTypes
{
    protected function resolveDeclaredType(\ReflectionProperty $rp): Type
    {
        $resolver = TypeResolver::create();
        $declared = $this->declaredVarType($rp);

        if (null !== $declared) {
            $expanded = AliasExpander::expand($declared, $rp->getDeclaringClass());

            if ($expanded !== $declared) {
                // Resolving a string loses the reflector, so the context has to be rebuilt or
                // an unqualified class name in the docblock no longer resolves.
                return $resolver->resolve($expanded, (new TypeContextFactory())->createFromReflection($rp));
            }
        }

        return $resolver->resolve($rp);
    }

    protected function declaredVarType(\ReflectionProperty $rp): ?string
    {
        $docblock = $rp->getDocComment();

        if (false === $docblock || preg_match('/^\s*\*\s*@var\s+(\S+)/m', $docblock, $match) !== 1) {
            return null;
        }

        return $match[1];
    }
}
