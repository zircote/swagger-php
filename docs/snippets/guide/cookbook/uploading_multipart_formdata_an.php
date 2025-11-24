<?php

namespace Openapi\Snippets\Cookbook\UploadingMultipartFormData;

use OpenApi\Annotations as OA;

class OpenApiSpec
{
    /**
     * @OA\Post(
     *     path="/v1/user/update",
     *     summary="Form post",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name"),
     *                 @OA\Property(
     *                     description="file to upload",
     *                     property="avatar",
     *                     type="string",
     *                     format="binary",
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function update()
    {
        // ...
    }
}
