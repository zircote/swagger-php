<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\MemberErrors;

use OpenApi\Spec as OA;

#[OA\Info(title: 'Inherited', version: '1.0')]
#[OA\PathItem(prefix: '/v1')]
class InheritedController extends AbstractController
{
}
