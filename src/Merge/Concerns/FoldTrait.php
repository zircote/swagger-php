<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Merge\Concerns;

use OpenApi\Merge\Mode;
use OpenApi\Undefined;

/**
 * Field-by-field folding for the mergers that combine two attributes rather than keep one.
 */
trait FoldTrait
{
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
     * @template T
     *
     * @param list<T>|null $earlier
     * @param list<T>|null $later
     *
     * @return list<T>|null
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
