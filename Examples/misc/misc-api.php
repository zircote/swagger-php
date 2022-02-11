<?php

namespace OpenApi\Examples\Misc;

/**
 * @OA\OpenApi(
 *     security={{"bearerAuth": {}}},
 *     @OA\Tag(
 *         name="endpoints"
 *     )
 * )
 *
 * @OA\Info(
 *     title="Testing annotations from bugreports",
 *     version="1.0.0",
 *     description="NOTE:
This sentence is on a new line",
 *     @OA\Contact(name="Swagger API Team")
 * )
 *
 * @OA\Components(
 *     @OA\SecurityScheme(
 *         securityScheme="bearerAuth",
 *         type="http",
 *         scheme="bearer",
 *     ),
 *     @OA\Attachable
 * )
 *
 * @OA\Server(
 *     url="{schema}://host.dev",
 *     description="OpenApi parameters",
 *     @OA\ServerVariable(
 *         serverVariable="schema",
 *         enum={"https", "http"},
 *         default="https"
 *     )
 * )
 */
class OpenApiSpec
{
}

/**
 * An API endpoint.
 *
 * @OA\Get(
 *     path="/api/endpoint",
 *     description="An endpoint",
 *     operationId="endpoint",
 *     tags={"endpoints"},
 *     @OA\Parameter(name="filter", in="query", @OA\JsonContent(
 *         @OA\Property(property="type", type="string"),
 *         @OA\Property(property="color", type="string"),
 *     )),
 *     security={{ "bearerAuth": {} }},
 *     @OA\Response(response="200", ref="#/components/responses/200")
 * )
 */
class Endpoint
{
}

/**
 * @OA\Response(
 *     response=200,
 *     description="Success",
 *     @OA\MediaType(
 *         mediaType="application/json",
 *         @OA\Schema(
 *             @OA\Property(property="name", type="integer", description="demo")
 *         ),
 *         @OA\Examples(example=200, summary="", value={"name": 1}),
 *         @OA\Examples(example=300, summary="", value={"name": 1}),
 *         @OA\Examples(example=400, summary="", value={"name": 1})
 *     )
 * )
 */
class Response
{
}

/**
 * Another API endpoint.
 *
 * @OA\Get(
 *     path="/api/anotherendpoint",
 *     description="Another endpoint",
 *     operationId="anotherendpoints",
 *     tags={"endpoints"},
 *     @OA\Parameter(
 *         name="things[]",
 *         in="query",
 *         description="A list of things.",
 *         required=false,
 *         @OA\Schema(
 *             type="array",
 *             @OA\Items(type="integer")
 *         )
 *     ),
 *     security={{ "bearerAuth": {} }},
 *     @OA\Response(response="200", ref="#/components/responses/200")
 * )
 */
class MultiValueQueryParam
{
}
