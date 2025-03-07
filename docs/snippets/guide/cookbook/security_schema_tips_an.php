<?php

class Controller
{
    /**
     * @OA\Get(
     *      path="/api/secure/",
     *      summary="Requires authentication"
     *    ),
     *    security={
     *      { "api_key": {} },
     *      { "petstore_auth": {"write:pets", "read:pets"} }
     *    }
     * )
     */
    public function secure()
    {
    }
}
