<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Assembler;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Contracts\AttributeTranslatorInterface;
use OpenApi\OpenApiException;
use OpenApi\Spec as OA;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Split an `OA\Operation` declaring `methods:` into one operation per method.
 *
 * The first method keeps the declared operation, every other one gets a `copy()` of it. When
 * the split operation is the only operation on its method, what would nest into it is set to
 * `Fan::All` where it leaves `fan` unset: its siblings, and the attributes on the method's PHP
 * parameters, or on `__invoke`'s for an operation declared on an invokable class. So each method gets the whole description. Next to other operations nothing is
 * fanned, and an attribute that could go into more than one of them is the usual ambiguity.
 *
 * An explicit `operationId` on several methods has to tell them apart with `{method}`, which
 * `Augmenter\OperationIdTemplates` expands. Without it, it is dropped with a warning and each
 * method gets a generated one.
 *
 * A translator creating `methods:` operations itself has to run before this one: `insert()` it
 * into the translators ahead of this class rather than `add()` it.
 *
 * @phpstan-import-type AttributeReflector from AttributeTranslatorInterface
 */
class OperationMethodsTranslator extends AbstractAttributeTranslator implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * Methods whose operation was split, by `Class::method`, for their parameters to fan out.
     *
     * @var array<string,true>
     */
    protected array $split = [];

    public function reset(): void
    {
        $this->split = [];
    }

    /**
     * @param  array<object>      $attributes current attributes; a previous translator's
     *                                        output, so not yet narrowed to AttributeInterface
     * @param  array<object>      $created    newly created attribute instances
     * @param  AttributeReflector $reflector
     * @return array<object>      attributes to carry forward; AttributeFactory filters
     *                            the final result down to AttributeInterface
     */
    public function translate(array $attributes, array $created, \ReflectionClass|\ReflectionMethod|\ReflectionProperty|\ReflectionParameter|\ReflectionClassConstant $reflector): array
    {
        $translated = [...$attributes, ...$created];

        // a method's parameters are read after the method, as reflectors of their own; what sits
        // on them nests into the split operations through contained(), so it fans out too
        if ($reflector instanceof \ReflectionParameter) {
            $function = $reflector->getDeclaringFunction();
            if ($function instanceof \ReflectionMethod && isset($this->split[$this->key($function)])) {
                $this->fanOut($translated, 'contained');
            }

            return $translated;
        }

        // counted before the split: a stacked operation beside the split one leaves fan alone
        $operations = array_filter($translated, static fn (object $attribute): bool => $attribute instanceof OA\Operation);

        $result = [];
        $fanned = false;
        foreach ($translated as $attribute) {
            if (!$attribute instanceof OA\Operation || $attribute->methods === null) {
                $result[] = $attribute;
                continue;
            }

            // in place, so the copies keep the declared operation's position among its siblings
            $split = $this->split($attribute);
            array_push($result, ...$split);
            $fanned = $fanned || (count($split) > 1 && count($operations) === 1);
        }

        if ($fanned) {
            // siblings nest through merge(), after every translator has run
            $this->fanOut($result, 'merge');

            // an invokable class declares its handler's operation on the class
            $handler = $reflector instanceof \ReflectionClass && $reflector->hasMethod('__invoke')
                ? $reflector->getMethod('__invoke')
                : $reflector;
            if ($handler instanceof \ReflectionMethod) {
                $this->split[$this->key($handler)] = true;
            }
        }

        return $result;
    }

    /**
     * @return list<OA\Operation>
     */
    protected function split(OA\Operation $operation): array
    {
        $methods = $operation->methods ?? [];

        if ($operation->method !== null) {
            throw OpenApiException::fromSource(
                sprintf('%s declares both `method` and `methods`, keep one of them', $operation::class),
                $operation->getSourceLocation(),
            );
        }

        // the copies are plain single-method operations from here on
        $operation->methods = null;

        if (count($methods) > 1 && $operation->operationId !== null && !str_contains($operation->operationId, '{method}')) {
            $this->logger?->warning(sprintf(
                'operationId "%s" ignored on an operation with several methods (%s), add {method} to tell them apart in %s',
                $operation->operationId,
                implode(', ', $methods),
                $operation->getSourceLocation(),
            ));
            $operation->operationId = null;
        }

        // the declared operation takes the first method, so what already holds it, a meta key
        // or the reflector, still finds it; copy() is deep, so no child is shared between methods
        $split = [];
        foreach ($methods as $position => $method) {
            $copy = $position === 0 ? $operation : $operation->copy();
            $copy->method = $method;
            $split[] = $copy;
        }

        // `methods: []` names none: left without a method, for the compiler to report
        return $split === [] ? [$operation] : $split;
    }

    /**
     * Set `Fan::All` on every attribute that nests into an operation by `$targets` and leaves
     * `fan` unset.
     *
     * @param array<object>       $attributes
     * @param 'merge'|'contained' $targets
     */
    protected function fanOut(array $attributes, string $targets): void
    {
        foreach ($attributes as $attribute) {
            // an explicit fan, Single included, is the author's and stays
            if (!$attribute instanceof AttributeInterface || $attribute instanceof OA\Operation || $attribute->getFan() instanceof OA\Fan) {
                continue;
            }

            foreach (array_keys($attribute->{$targets}()) as $type) {
                // a target naming Operation or one of its parents takes the split operations
                if (is_a(OA\Operation::class, $type, true)) {
                    $attribute->setFan(OA\Fan::All);
                    break;
                }
            }
        }
    }

    protected function key(\ReflectionMethod $method): string
    {
        return $method->getDeclaringClass()->getName() . '::' . $method->getName();
    }
}
