<?php

namespace PetstoreSimple;

class PetResource {

    /**
     * @SWG\Get(
     *     path="/pets",
     *     summary="finds pets in the system",
     *     tags={"Pet Operations"},
     *     @SWG\Response(
     *         code=200,
     *         description="pet response",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref="#/definitions/Pet")
     *         ),
     *         @SWG\Header(header="x-expires", type="string")
     *     ),
     *     @SWG\Response(
     *         code="default",
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
