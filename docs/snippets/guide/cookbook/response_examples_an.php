<?php

class Controller
{
    /**
     * @OA\Schema(
     *   schema="Result",
     *   type="object",
     *   properties={
     *     @OA\Property(property="success", type="boolean"),
     *   },
     * )
     * @OA\Response(
     *   response=200,
     *   description="OK",
     *   @OA\JsonContent(
     *     oneOf={
     *       @OA\Schema(ref="#/components/schemas/Result"),
     *       @OA\Schema(type="boolean")
     *     },
     *     @OA\Examples(example="result", value={"success": true}, summary="An result object."),
     *     @OA\Examples(example="bool", value=false, summary="A boolean value."),
     *   )
     * )
     */
    public function operation()
    {
    }
}
