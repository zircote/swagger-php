<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

enum XmlNodeType: string
{
    case Element = 'element';
    case Attribute = 'attribute';
    case Text = 'text';
    case Cdata = 'cdata';
    case None = 'none';
}
