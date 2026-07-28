<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

enum ParameterStyle: string
{
    case Matrix = 'matrix';
    case Label = 'label';
    case Form = 'form';
    case Simple = 'simple';
    case SpaceDelimited = 'spaceDelimited';
    case PipeDelimited = 'pipeDelimited';
    case DeepObject = 'deepObject';
}
