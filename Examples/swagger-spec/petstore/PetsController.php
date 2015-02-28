<?php

namespace Petstore;

class PetsController {

    /**
     * @SWG\Get(
     *     path="/pets",
     *     summary="finds pets in the system",
     *     tags={"Pet Operations"},
     *     @SWG\Response(
     *         name=200,
     *         description="pet response",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref="#/definitions/Pet")
     *         ),
     *         @SWG\Header(header="x-expires", type="string")
     *     ),
     *     @SWG\Response(
     *         name="default",
     *         description="unexpected error",
     *         @SWG\Schema(
     *             ref="#/definitions/Error"
     *         )
     *     )
     * )
     */
    function findPets() {

    }

}
