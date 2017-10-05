<?php
namespace PetstoreWithExternalDocs\Controller;

class PetController {

   /**
     * @Operation(
     *     tags={"Pet"},
     *     summary="Insert a new pet",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     ),
     *     @SWG\Parameter(
     *        name="pet",
     *        in="body",
     *        required=true,
     *        @SWG\Schema(
     *           @Model(type=PetstoreWithExternalDocs\Model\Pet::class)
     *        )
     *     )
     * )
     *
     * @Put("/pet/insert/")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function insertPetAction(Request $request) {
    
    }
}
