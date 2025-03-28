<?php

class Controller
{
    /**
     * @OA\Post(
     *   path="/v1/media/upload",
     *   summary="Upload document",
     *   description="",
     *   tags={"Media"},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *       mediaType="application/octet-stream",
     *       @OA\Schema(
     *         required={"content"},
     *         @OA\Property(
     *           description="Binary content of file",
     *           property="content",
     *           type="string",
     *           format="binary"
     *         )
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=200, description="Success",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=400, description="Bad Request"
     *   )
     * )
     */
    public function upload()
    {
        // ...
    }
}
