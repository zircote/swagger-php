<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

enum ParameterIn: string
{
    case Query = 'query';
    case Header = 'header';
    case Path = 'path';
    case Cookie = 'cookie';
}
