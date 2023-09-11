<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * @Annotation
 */
class Webhook extends AbstractAnnotation
{
    /**
     * @var string
     */
    public $name = Generator::UNDEFINED;

    /**
     * @var PathItem
     */
    public $pathItem = Generator::UNDEFINED;
}
