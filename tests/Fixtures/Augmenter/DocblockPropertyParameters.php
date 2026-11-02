<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

class DocblockPropertyParameters
{
    /**
     * The page size.
     *
     * @example 20
     * @deprecated
     */
    public int $limit;

    /**
     * @var int the offset
     * @example 0
     */
    public int $offset;

    /**
     * @param int $pageSize the constructor parameter, not the property
     */
    public function __construct(
        /**
         * The promoted page size.
         *
         * @example 10
         * @deprecated
         */
        public int $pageSize = 10,
    ) {
    }
}
