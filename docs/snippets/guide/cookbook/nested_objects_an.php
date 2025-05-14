<?php

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Profile",
 *     type="object",
 *
 *     @OA\Property(
 *         property="Status",
 *         type="string",
 *         example="0"
 *     ),
 *
 *     @OA\Property(
 *         property="Group",
 *         type="object",
 *
 *         @OA\Property(
 *             property="ID",
 *             description="ID de grupo",
 *             type="number",
 *             example=-1
 *         ),
 *
 *         @OA\Property(
 *             property="Name",
 *             description="Nombre de grupo",
 *             type="string",
 *             example="Superadmin"
 *         )
 *     )
 * )
 */
class OpenApiSpec
{
}
