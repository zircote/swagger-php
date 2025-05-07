<?php

use OpenApi\Annotations as OA;

/**
 * @Annotation
 */
class BadRequest extends OA\Response
{
    public function __construct()
    {
        parent::__construct(response: 400, description: 'Bad request');
    }
}

class Controller
{

    /**
     * @OA\Get(
     *     path="/foo",
     *     @BadRequest()
     * )
     */
    public function get() {}

    /**
     * @OA\Post(
     *     path="/foo",
     * )
     * @BadRequest()
     */
    public function post() {}

    /**
     * @OA\Delete(
     *     path="/foo",
     *     @BadRequest()
     * )
     */
    public function delete() {}
}
