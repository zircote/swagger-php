<?php

use OpenApi\Annotations as OA;

/**
 *     ...
 *
 *     callbacks={
 *         "onChange"={
 *              "{$request.query.callbackUrl}"={
 *                  "post": {
 *                      "requestBody": @OA\RequestBody(
 *                          description="subscription payload",
 *                          @OA\MediaType(mediaType="application/json", @OA\Schema(
 *                              @OA\Property(property="timestamp", type="string", format="date-time", description="time of change")
 *                          ))
 *                      )
 *                  },
 *                  "responses": {
 *                      "202": {
 *                          "description": "Your server implementation should return this HTTP status code if the data was received successfully"
 *                      }
 *                  }
 *              }
 *         }
 *     }
 *
 *     ...
 *
 */
class Controller {}
