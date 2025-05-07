<?php

use OpenApi\Attributes as OA;

/**
 * @Annotation
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class BadRequest extends OA\Response
{
    public function __construct()
    {
        parent::__construct(response: 400, description: 'Bad request');
    }
}

class Controller
{

    #[OA\Get(path: '/foo', responses: [new BadRequest()])]
    public function get() {}

    #[OA\Post(path: '/foo')]
    #[BadRequest]
    public function post() {}

    #[OA\Delete(path: '/foo', responses: [new BadRequest()])]
    public function delete() {}
}
