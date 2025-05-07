<?php

use OpenApi\Annotations as OA;

/** @OA\Schema() */
enum State
{
    case OPEN;
    case MERGED;
    case DECLINED;
}

/** @OA\Schema() */
class PullRequest
{
    /** @OA\Property */
    public State $state;
}