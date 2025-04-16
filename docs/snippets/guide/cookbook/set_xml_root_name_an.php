<?php

/**
 * @OA\Schema(
 *     schema="Error",
 *     @OA\Property(property="message"),
 *     @OA\Xml(name="details")
 * )
 * @OA\Post(
 *     path="/foobar",
 *     @OA\Response(
 *         response=400,
 *         description="Request error",
 *         @OA\XmlContent(ref="#/components/schemas/Error",
 *           @OA\Xml(name="error")
 *        )
 *     )
 * )
 */
class OpenApiSpec
{
}
