<?php

namespace OpenApi\Examples\UsingLinksPhp81;

use OpenApi\Attributes as OAT;

class RepositoriesController
{
    #[OAT\Get(
        path: '/2.0/repositories/{username}',
        operationId: 'getRepositoriesByOwner',
        parameters: [
            new OAT\Parameter(name: 'username', in: 'path', required: true, schema: new OAT\Schema(type: 'string')),
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Repositories owned by the supplied user',
                content: new OAT\JsonContent(
                    type: 'array',
                    items: new OAT\Items(ref: '#/components/schemas/repository')
                ),
                links: [
                    new OAT\Link(link: 'userRepository', ref: '#/components/links/UserRepository'),
                ]
            ),
        ]
    )
    ]
    #[OAT\Link(link: 'UserRepositories', operationId: 'getRepositoriesByOwner', parameters: ['username' => '$response.body#/username'])]
    public function getRepositoriesByOwner($username)
    {
    }

    #[OAT\Get(
        path: '/2.0/repositories/{username}/{slug}',
        operationId: 'getRepository',
        parameters: [
            new OAT\Parameter(name: 'username', in: 'path', required: true, schema: new OAT\Schema(type: 'string')),
            new OAT\Parameter(name: 'slug', in: 'path', required: true, schema: new OAT\Schema(type: 'string')),
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'The repository',
                content: new OAT\JsonContent(ref: '#/components/schemas/repository'),
                links: [
                    new OAT\Link(link: 'repositoryPullRequests', ref: '#/components/links/RepositoryPullRequests'),
                ]
            ),
        ]
    )
    ]
    #[OAT\Link(link: 'UserRepository', operationId: 'getRepository', parameters: ['username' => '$response.body#/owner/username', 'slug' => '$response.body#/slug'])]
    public function getRepository()
    {
    }

    #[OAT\Get(
        path: '/2.0/repositories/{username}/{slug}/{state}/pullrequests',
        operationId: 'getPullRequestsByRepository',
        responses: [
            new OAT\Response(response: 200, description: 'An array of pull request objects', content: new OAT\JsonContent(type: 'array', items: new OAT\Items(ref: '#/components/schemas/pullrequest'))),
        ]
    )
    ]
    #[OAT\Link(link: 'RepositoryPullRequests', operationId: 'getPullRequestsByRepository', parameters: ['username' => '$response.body#/owner/username', 'slug' => '$response.body#/slug'])]
    public function getPullRequestsByRepository(
        #[OAT\PathParameter()] string $username,
        #[OAT\PathParameter()] string $slug,
        #[OAT\PathParameter()] State $state
    ) {
    }

    #[OAT\Get(
        path: '/2.0/repositories/{username}/{slug}/pullrequests/{pid}',
        operationId: 'getPullRequestsById',
        parameters: [
            new OAT\Parameter(name: 'username', in: 'path', required: true, schema: new OAT\Schema(type: 'string')),
            new OAT\Parameter(name: 'slug', in: 'path', required: true, schema: new OAT\Schema(type: 'string')), new OAT\Parameter(name: 'pid', in: 'path', required: true, schema: new OAT\Schema(type: 'string')),
        ],
        responses: [
            new OAT\Response(response: 200, description: 'A pull request object', content: new OAT\JsonContent(ref: '#/components/schemas/pullrequest'), links: [new OAT\Link(link: 'pullRequestMerge', ref: '#/components/links/PullRequestMerge')]),
        ]
    )
    ]
    public function getPullRequestsById()
    {
    }

    #[OAT\Post(
        path: '/2.0/repositories/{username}/{slug}/pullrequests/{pid}/merge',
        operationId: 'mergePullRequest',
        parameters: [
            new OAT\Parameter(name: 'username', in: 'path', required: true, schema: new OAT\Schema(type: 'string')),
            new OAT\Parameter(name: 'slug', in: 'path', required: true, schema: new OAT\Schema(type: 'string')),
            new OAT\Parameter(name: 'pid', in: 'path', required: true, schema: new OAT\Schema(type: 'string')),
        ],
        responses: [
            new OAT\Response(response: 204, description: 'The PR was successfully merged'),
        ]
    )
    ]
    #[OAT\Link(link: 'PullRequestMerge', operationId: 'mergePullRequest', parameters: ['username' => '$response.body#/author/username', 'slug' => '$response.body#/repository/slug', 'pid' => '$response.body#/id'])]
    public function mergePullRequest()
    {
    }
}
