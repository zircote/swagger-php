<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace Swagger\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION", "PROPERTY"})
 */
class Post extends Operation
{
    /** @inheritdoc */
    public $method = 'post';

    /** @inheritdoc */
    public static $_parents = [
        'Swagger\Annotations\PathItem'
    ];
}
