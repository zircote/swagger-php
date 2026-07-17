<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingLinks\Spec;

use OpenApi\Spec as OA;

class RepositoriesController
{
    #[OA\Operation\Get(
        path: '/2.0/repositories/{username}',
        operationId: 'getRepositoriesByOwner',
        parameters: [
            new OA\Parameter(name: 'username', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Repositories owned by the supplied user',
                content: [new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(type: 'array', items: new OA\Schema(ref: '#/components/schemas/repository')))],
                links: [new OA\Link(link: 'userRepository', ref: '#/components/links/UserRepository')],
            ),
        ],
    )]
    #[OA\Link(link: 'UserRepositories', operationId: 'getRepositoriesByOwner', parameters: ['username' => '$response.body#/username'])]
    public function getRepositoriesByOwner($username)
    {
    }

    #[OA\Operation\Get(
        path: '/2.0/repositories/{username}/{slug}',
        operationId: 'getRepository',
        parameters: [
            new OA\Parameter(name: 'username', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'slug', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'The repository',
                content: [new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(ref: '#/components/schemas/repository'))],
                links: [new OA\Link(link: 'repositoryPullRequests', ref: '#/components/links/RepositoryPullRequests')],
            ),
        ],
    )]
    #[OA\Link(link: 'UserRepository', operationId: 'getRepository', parameters: ['username' => '$response.body#/owner/username', 'slug' => '$response.body#/slug'])]
    public function getRepository()
    {
    }

    #[OA\Operation\Get(
        path: '/2.0/repositories/{username}/{slug}/{state}/pullrequests',
        operationId: 'getPullRequestsByRepository',
        responses: [
            new OA\Response(
                response: 200,
                description: 'An array of pull request objects',
                content: [new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(type: 'array', items: new OA\Schema(ref: '#/components/schemas/pullrequest')))],
            ),
        ],
    )]
    #[OA\Link(link: 'RepositoryPullRequests', operationId: 'getPullRequestsByRepository', parameters: ['username' => '$response.body#/owner/username', 'slug' => '$response.body#/slug'])]
    public function getPullRequestsByRepository(
        #[OA\Parameter\Path]
        string $username,
        #[OA\Parameter\Path]
        string $slug,
        #[OA\Parameter\Path]
        State $state,
        #[OA\Parameter\Query]
        ?string $label,
    ) {
    }

    #[OA\Operation\Get(
        path: '/2.0/repositories/{username}/{slug}/pullrequests/{pid}',
        operationId: 'getPullRequestsById',
        parameters: [
            new OA\Parameter(name: 'username', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'slug', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'pid', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'A pull request object',
                content: [new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(ref: '#/components/schemas/pullrequest'))],
                links: [new OA\Link(link: 'pullRequestMerge', ref: '#/components/links/PullRequestMerge')],
            ),
        ],
    )]
    public function getPullRequestsById()
    {
    }

    #[OA\Operation\Post(
        path: '/2.0/repositories/{username}/{slug}/pullrequests/{pid}/merge',
        operationId: 'mergePullRequest',
        parameters: [
            new OA\Parameter(name: 'username', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'slug', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'pid', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'The PR was successfully merged'),
        ],
    )]
    #[OA\Link(link: 'PullRequestMerge', operationId: 'mergePullRequest', parameters: ['username' => '$response.body#/author/username', 'slug' => '$response.body#/repository/slug', 'pid' => '$response.body#/id'])]
    public function mergePullRequest(
        #[OA\Parameter\Header(name: 'X-NONCE-ID')]
        string $nonceId,
        #[OA\Parameter\Cookie(name: 'User-Bind-Session')]
        ?string $bindCookie,
    ) {
    }
}
