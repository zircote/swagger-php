<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Annotations;

use OpenApi\Annotations as OA;
use OpenApi\Generator;

/**
 * @Annotation
 */
class CustomAttachable extends OA\Attachable
{
    /**
     * The attribute value.
     *
     * @var mixed
     */
    public $value = Generator::UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_required = ['value'];

    public function allowedParents(): ?array
    {
        return [OA\Operation::class];
    }
}
