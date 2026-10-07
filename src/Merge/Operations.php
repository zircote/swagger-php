<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Merge;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Contracts\MergerInterface;
use OpenApi\Merge\Concerns\IdentityTrait;
use OpenApi\Merge\Concerns\ModeTrait;
use OpenApi\Spec as OA;
use OpenApi\Undefined;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Folds two halves of one operation field by field, instead of keeping one of them whole.
 *
 * Opt-in: it is not one of the Builder's default mergers. Register it ahead of the catch-all,
 * `withMergers(fn ($mergers) => $mergers->insert(new Merge\Operations(), Merge\LastWins::class))`,
 * when one operation is described in more than one place, such as a route table contributed
 * through `withSpecification()` and an attribute adding responses to it.
 *
 * Identity is the catch-all's: path and method, or webhook and method. Registering the fold
 * changes how two operations combine, not which two are the same.
 *
 * What only one half sets is always taken. What both set is decided by the `Mode` it is
 * constructed with, `Mode::Last` by default: fold into the later half, fold into the earlier, or
 * take only what does not overlap. The mode can also be a callable that receives both halves and
 * returns one, so the choice can follow a mark a producer left with `setMeta()` rather than the
 * order the halves arrive in. A field both halves set differently is reported, whichever value
 * is kept; one they set the same is not.
 *
 * - Parameters are keyed by name and location, and responses by status code. Under `First` and
 *   `Last`, a parameter or response both describe is folded one level deep, a parameter's
 *   `schema` included, so a route's `pattern` survives an attribute's `type`. Under
 *   `NonOverlapping` it is the earlier half's, whole.
 * - `requestBody`, `externalDocs`, `security`, `servers` and `callbacks` are each one decision,
 *   taken whole from whichever half the mode keeps; an explicit `security: []` is set, and opts
 *   out.
 * - `tags` and `attachables` are unions, and `x` merges key by key under the same mode.
 *
 * The survivor is a new operation, a copy of the earlier half with the later folded in, so
 * neither contribution is changed. It keeps the earlier half's reflector, source location and
 * `meta`, falling back to the later half's reflector when the earlier has none.
 */
class Operations implements MergerInterface, LoggerAwareInterface
{
    use IdentityTrait;
    use LoggerAwareTrait;
    use ModeTrait;

    /**
     * @param Mode|callable(AttributeInterface, AttributeInterface): Mode $mode
     */
    public function __construct(Mode|callable $mode = Mode::Last)
    {
        $this->setMode($mode);
    }

    public function supports(string $class): bool
    {
        return is_a($class, OA\Operation::class, true);
    }

    public function merge(AttributeInterface $earlier, AttributeInterface $later): AttributeInterface
    {
        // supports() claims operations only; anything else is kept as the catch-all would keep it
        if (!$earlier instanceof OA\Operation || !$later instanceof OA\Operation) {
            $this->logger?->warning($this->collision($earlier, $later));

            return $later;
        }

        $mode = $this->modeFor($earlier, $later);

        $survivor = clone $earlier;
        if (!$survivor->getReflector() instanceof \Reflector && $later->getReflector() instanceof \Reflector) {
            $survivor->setReflector($later->getReflector());
        }

        $conflicts = [];

        $this->fold($survivor, $later, ['operationId', 'summary', 'description', 'deprecated', 'requestBody', 'externalDocs', 'security', 'servers', 'callbacks'], $mode, $conflicts);

        $survivor->tags = $this->union($survivor->tags, $later->tags);
        $survivor->attachables = $this->union($survivor->attachables, $later->attachables);
        $survivor->x = $this->extensions($survivor->x, $later->x, $mode, $conflicts);
        $survivor->parameters = $this->keyed($survivor->parameters, $later->parameters, $this->parameterKey(...), 'parameter', $mode, $conflicts);
        $survivor->responses = $this->keyed($survivor->responses, $later->responses, $this->responseKey(...), 'response', $mode, $conflicts);

        if ($conflicts !== []) {
            $this->logger?->warning(sprintf(
                '%s; %s set differently',
                $this->collision($earlier, $later, $mode->outcome()),
                implode(', ', array_unique($conflicts)),
            ));
        }

        return $survivor;
    }

    /**
     * Fill each named field of `$survivor` that is unset from `$later`. Where both are set and
     * differ, the field is recorded as a conflict and takes `$later`'s value under `Mode::Last`;
     * otherwise it keeps its own.
     *
     * @param list<string> $fields
     * @param list<string> $conflicts
     */
    protected function fold(object $survivor, object $later, array $fields, Mode $mode, array &$conflicts, string $prefix = ''): void
    {
        foreach ($fields as $field) {
            $value = $later->{$field};
            if (self::isUnset($value)) {
                continue;
            }

            $current = $survivor->{$field};
            if (self::isUnset($current)) {
                $survivor->{$field} = $value;
                continue;
            }

            if ($current != $value) {
                $conflicts[] = $prefix . $field;
                if ($mode === Mode::Last) {
                    $survivor->{$field} = $value;
                }
            }
        }
    }

    /**
     * One level deep: the public fields of two halves of a parameter or a response, and of a
     * parameter's schema, folded by the same rule. Deeper structure is taken whole.
     *
     * @param list<string> $conflicts
     */
    protected function foldChild(OA\AbstractAttribute $earlier, OA\AbstractAttribute $later, string $label, Mode $mode, array &$conflicts): OA\AbstractAttribute
    {
        $survivor = clone $earlier;

        $fields = array_keys(array_filter(
            get_object_vars($later),
            static fn (string $field): bool => !in_array($field, ['x', 'attachables', 'schema'], true),
            ARRAY_FILTER_USE_KEY,
        ));
        $this->fold($survivor, $later, $fields, $mode, $conflicts, $label . '.');

        $survivor->attachables = $this->union($survivor->attachables, $later->attachables);
        $survivor->x = $this->extensions($survivor->x, $later->x, $mode, $conflicts, $label . '.');

        if ($survivor instanceof OA\Parameter && $later instanceof OA\Parameter) {
            if ($survivor->schema instanceof OA\Schema && $later->schema instanceof OA\Schema) {
                $schema = clone $survivor->schema;
                $this->fold($schema, $later->schema, array_keys(get_object_vars($later->schema)), $mode, $conflicts, $label . '.schema.');
                $survivor->schema = $schema;
            } elseif ($later->schema instanceof OA\Schema) {
                $survivor->schema = $later->schema;
            }
        }

        return $survivor;
    }

    /**
     * Entries keyed by `$key` are combined: a key only one side has is taken, and a key both
     * have is folded with `foldChild()`, or under `Mode::NonOverlapping` left as the earlier
     * side's, with the fields that differ still reported.
     *
     * @template T of OA\AbstractAttribute
     *
     * @param list<T>|null               $earlier
     * @param list<T>|null               $later
     * @param callable(T): (string|null) $key       null for an entry with no identity, which is kept as it is
     * @param list<string>               $conflicts
     *
     * @return list<T>|null
     */
    protected function keyed(?array $earlier, ?array $later, callable $key, string $label, Mode $mode, array &$conflicts): ?array
    {
        if ($later === null || $later === []) {
            return $earlier;
        }
        if ($earlier === null || $earlier === []) {
            return $later;
        }

        $result = [];
        $slots = [];
        foreach ([...$earlier, ...$later] as $item) {
            $identity = $item instanceof OA\AbstractAttribute ? $key($item) : null;
            if ($identity === null) {
                $result[] = $item;
                continue;
            }

            if (!array_key_exists($identity, $slots)) {
                $slots[$identity] = count($result);
                $result[] = $item;
                continue;
            }

            /** @var T $folded */
            $folded = $this->foldChild($result[$slots[$identity]], $item, $label . ' ' . $identity, $mode, $conflicts);
            if ($mode !== Mode::NonOverlapping) {
                $result[$slots[$identity]] = $folded;
            }
        }

        return $result;
    }

    protected function parameterKey(OA\AbstractAttribute $parameter): ?string
    {
        return $parameter instanceof OA\Parameter && $parameter->name !== null && $parameter->in !== null
            ? $parameter->in . ':' . $parameter->name
            : null;
    }

    protected function responseKey(OA\AbstractAttribute $response): ?string
    {
        return $response instanceof OA\Response && $response->response !== null
            ? (string) $response->response
            : null;
    }

    /**
     * @param array<mixed>|null $earlier
     * @param array<mixed>|null $later
     *
     * @return array<mixed>|null
     */
    protected function union(?array $earlier, ?array $later): ?array
    {
        if ($later === null) {
            return $earlier;
        }
        if ($earlier === null) {
            return $later;
        }

        $result = $earlier;
        foreach ($later as $item) {
            if (!in_array($item, $result, true)) {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * Key by key: a key only one side has is taken, and one both have differently is a conflict
     * resolved by the mode.
     *
     * @param array<string, mixed>|null $earlier
     * @param array<string, mixed>|null $later
     * @param list<string>              $conflicts
     *
     * @return array<string, mixed>|null
     */
    protected function extensions(?array $earlier, ?array $later, Mode $mode, array &$conflicts, string $prefix = ''): ?array
    {
        if ($later === null) {
            return $earlier;
        }
        if ($earlier === null) {
            return $later;
        }

        $result = $earlier;
        foreach ($later as $key => $value) {
            if (!array_key_exists($key, $result)) {
                $result[$key] = $value;
                continue;
            }

            if ($result[$key] != $value) {
                $conflicts[] = $prefix . 'x.' . $key;
                if ($mode === Mode::Last) {
                    $result[$key] = $value;
                }
            }
        }

        return $result;
    }

    protected static function isUnset(mixed $value): bool
    {
        return $value === null || Undefined::isDefault($value);
    }
}
