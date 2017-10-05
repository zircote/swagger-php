<?php
namespace PetstoreWithExternalDocs\Controller;

class PetController {

   /**
     * @Operation(
     *     tags={"DeliverySlip"},
     *     summary="Send information after deliveryItems are processed and deliverySlip was scanned",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     ),
     *     @SWG\Parameter(
     *        name="JSON update body",
     *        in="body",
     *        description="josn - information for a pet",
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
    public function insertPetSlipAction(Request $request) {
    
    }
}
