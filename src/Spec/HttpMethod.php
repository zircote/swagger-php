<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

enum HttpMethod: string
{
    case Get = 'get';
    case Put = 'put';
    case Post = 'post';
    case Delete = 'delete';
    case Options = 'options';
    case Head = 'head';
    case Patch = 'patch';
    case Trace = 'trace';
}
