<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

enum Group: string
{
    case Resolve = 'resolve';
    case Reduce = 'reduce';
    case Augment = 'augment';
}
