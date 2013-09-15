<?php
use Swagger\Annotations as SWG;


/**
 * @SWG\Operation(
 *   partial="crud/create",
 *   method="POST",
 *   summary="Create or add new entries",
 *   nickname="partial"
 * )
 */

/**
 * @SWG\Operation(
 *   partial="crud/read",
 *   method="GET",
 *   summary="Read, retrieve, search, or view existing entries",
 *   nickname="partial",
 *   @SWG\Partial("param_id")
 * )
 */

/**
 * @SWG\Parameter(
 *   partial="param_id",
 *   name="id",
 *   description="The unique ID",
 *   paramType="path",
 *   required="true",
 *   type="string"
 * )
 */
