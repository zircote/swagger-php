<?php

namespace OpenApi\LinkExample;

class RepositoriesController
{

    /**
     * @OAS\Get(path="/2.0/repositories/{username}",
     *   operationId="getRepositoriesByOwner",
     *   @OAS\Parameter(name="username",
     *     in="path",
     *     required=true,
     *     @OAS\Schema(type="string")
     *   ),
     *   @OAS\Response(response=200,
     *     description="repositories owned by the supplied user",
     *     @OAS\JsonContent(type="array",
     *       @OAS\Items(ref="#/components/schemas/repository")
     *     ),
     *     @OAS\Link(link="userRepository", ref="#/components/links/UserRepository")
     *   )
     * )
     *
     * @OAS\Link(link="UserRepositories",
     *   operationId="getRepositoriesByOwner",
     *   parameters={"username"="$response.body#/username"}
     * )
     */
    public function getRepositoriesByOwner($username)
    {
    }

    /**
     ** @OAS\Get(path="/2.0/repositories/{username}/{slug}",
     *   operationId="getRepository",
     *   @OAS\Parameter(name="username",
     *     in="path",
     *     required=true,
     *     @OAS\Schema(type="string")
     *   ),
     *   @OAS\Parameter(name="slug",
     *     in="path",
     *     required=true,
     *     @OAS\Schema(type="string")
     *   ),
     *   @OAS\Response(response=200,
     *       description="The repository",
     *       @OAS\JsonContent(ref="#/components/schemas/repository"),
     *       @OAS\Link(link="repositoryPullRequests", ref="#/components/links/RepositoryPullRequests")
     *     )
     *   )
     * )
     *
     * @OAS\Link(link="UserRepository",
     *   operationId="getRepository",
     *   parameters={
     *     "username"="$response.body#/owner/username",
     *     "slug"="$response.body#/slug"
     *   }
     * )
     */
    public function getRepository()
    {
    }

    /**
     * @OAS\Get(path="/2.0/repositories/{username}/{slug}/pullrequests",
     *   operationId="getPullRequestsByRepository",
     *   @OAS\Parameter(name="username",
     *     in="path",
     *     required=true,
     *     @OAS\Schema(type="string")
     *   ),
     *   @OAS\Parameter(name="slug",
     *     in="path",
     *     required=true,
     *     @OAS\Schema(type="string")
     *   ),
     *   @OAS\Parameter(name="state",
     *     in="query",
     *     @OAS\Schema(type="string",
     *       enum={"open", "merged", "declined"}
     *     )
     *   ),
     *   @OAS\Response(response=200,
     *     description="an array of pull request objects",
     *     @OAS\JsonContent(type="array",
     *         @OAS\Items(ref="#/components/schemas/pullrequest")
     *     )
     *   )
     * )
     *
     * @OAS\Link(link="RepositoryPullRequests",
     *   operationId="getPullRequestsByRepository",
     *   parameters={
     *     "username"="$response.body#/owner/username",
     *     "slug"="$response.body#/slug"
     *   }
     * )
     */
    public function getPullRequestsByRepository()
    {
    }

    /**
     * @OAS\Get(path="/2.0/repositories/{username}/{slug}/pullrequests/{pid}",
     *   operationId="getPullRequestsById",
     *   @OAS\Parameter(name="username",
     *     in="path",
     *     required=true,
     *     @OAS\Schema(type="string")
     *   ),
     *   @OAS\Parameter(name="slug",
     *     in="path",
     *     required=true,
     *     @OAS\Schema(type="string")
     *   ),
     *   @OAS\Parameter(name="pid",
     *     in="path",
     *     required=true,
     *     @OAS\Schema(type="string")
     *   ),
     *   @OAS\Response(response=200,
     *     description="a pull request object",
     *     @OAS\JsonContent(ref="#/components/schemas/pullrequest"),
     *     @OAS\Link(link="pullRequestMerge", ref="#/components/links/PullRequestMerge")
     *   )
     * )
     */
    public function getPullRequestsById()
    {
    }

    /**
     * @OAS\Post(path="/2.0/repositories/{username}/{slug}/pullrequests/{pid}/merge",
     *   operationId="mergePullRequest",
     *   @OAS\Parameter(name="username",
     *     in="path",
     *     required=true,
     *     @OAS\Schema(type="string")
     *   ),
     *   @OAS\Parameter(name="slug",
     *     in="path",
     *     required=true,
     *     @OAS\Schema(type="string")
     *   ),
     *   @OAS\Parameter(name="pid",
     *     in="path",
     *     required=true,
     *     @OAS\Schema(type="string")
     *   ),
     *   @OAS\Response(response=204,
     *     description="the PR was successfully merged"
     *   )
     * )
     *
     * @OAS\Link(link="PullRequestMerge",
     *   operationId="mergePullRequest",
     *   parameters={
     *     "username"="$response.body#/author/username",
     *     "slug"="$response.body#/repository/slug",
     *     "pid"="$response.body#/id"
     *   }
     * )
     */
    public function mergePullRequest()
    {
    }
}
?>




