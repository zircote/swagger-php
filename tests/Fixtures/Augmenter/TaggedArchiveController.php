<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

#[OA\PathItem(prefix: '/archive', tags: ['Archive'])]
#[OA\Tag(name: 'archived')]
final class TaggedArchiveController extends AbstractDocumentController
{
}
