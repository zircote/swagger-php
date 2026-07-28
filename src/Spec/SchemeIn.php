<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

enum SchemeIn: string
{
    case Query = 'query';
    case Header = 'header';
    case Cookie = 'cookie';
}
