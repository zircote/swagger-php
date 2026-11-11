<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

/**
 * Uses of a generic class.
 */
class GenericBoxHolder
{
    /** @var GenericBox<int> */
    public GenericBox $box;

    /** @var GenericBox<RefTarget> */
    public GenericBox $undescribed;

    /** @var GenericBox<int>|RefTarget */
    public GenericBox|RefTarget $either;

    /** @var GenericBox<int>|BackedStringEnum */
    public GenericBox|BackedStringEnum $boxOrEnum;

    /** @var GenericBox<BackedStringEnum> */
    public GenericBox $boxedEnum;

    /**
     * @return GenericBox<string>
     */
    public function boxed(): GenericBox
    {
        return new GenericBox();
    }
}
