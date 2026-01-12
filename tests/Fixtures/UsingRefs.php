<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="Using a parameter definition", version="unittest")
 */
class UsingRefs
{
<<<<<<< HEAD
    /**
     * @OA\Get(
     *     path="/pi/{item_name}",
     *     summary="Get protected item",
     *     @OA\Parameter(ref="#/components/parameters/ItemName"),
     *     @OA\Response(
     *         response="default",
     *         ref="#/components/responses/Item"
     *     )
     * )
     */
=======
    #[OAT\Get(
        path: '/pi/{item_name}',
        summary: 'Get protected item',
        parameters: [new OAT\Parameter(ref: '#/components/parameters/ItemName')],
        responses: [new OAT\Response(response: 'default', ref: '#/components/responses/default')],
    )]
>>>>>>> 8cc63b7 (Update `phpunit` deps and some related issues (#1909))
    public function getProtectedItem()
    {
    }
}

/**
 * @OA\Parameter(
 *     name="ItemName",
 *     in="path",
 *     required=true,
 *     description="protected item name",
 * )
 */
class UsingRefsParameter
{
}

<<<<<<< HEAD
/**
 * @OA\Response(
 *     response="Item",
 *     description="A protected item"
 * )
 */
=======
#[OAT\Response(response: 'default', description: 'A protected item')]
>>>>>>> 8cc63b7 (Update `phpunit` deps and some related issues (#1909))
class UsingRefsResponse
{
}
