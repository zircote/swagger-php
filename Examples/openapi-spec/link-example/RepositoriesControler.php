<?php

namespace OpenApi\LinkExample;

/**

 *
 *
 *

 */
class RepositoriesController
{

    /**
     * @SWG\Get(path="/2.0/repositories/{username}",
     *   operationId="getRepositoriesByOwner",
     *   @SWG\Parameter(name="username",
     *     in="path",
     *     required=true,
     *     @SWG\Schema(type="string")
     *   ),
     *   @SWG\Response(response=200,
     *     description="repositories owned by the supplied user",
     *     @SWG\MediaType(mediaType="application/json",
     *       @SWG\Schema(type="array",
     *         @SWG\Items(ref="#/components/schemas/repository")
     *       )
     *     ),
     *     @SWG\Link(link="userRepository", ref="#/components/links/UserRepository")
     *   )
     * )
     *
     * @SWG\Link(link="UserRepositories",
     *   operationId="getRepositoriesByOwner",
     *   parameters={"username"="$response.body#/username"}
     * )
     */
    public function getRepositoriesByOwner($username)
    {
    }

    /**
     ** @SWG\Get(path="/2.0/repositories/{username}/{slug}",
     *   operationId="getRepository",
     *   @SWG\Parameter(name="username",
     *     in="path",
     *     required=true,
     *     @SWG\Schema(type="string")
     *   ),
     *   @SWG\Parameter(name="slug",
     *     in="path",
     *     required=true,
     *     @SWG\Schema(type="string")
     *   ),
     *   @SWG\Response(response=200,
     *       description="The repository",
     *       @SWG\MediaType(mediaType="application/json",
     *           @SWG\Schema(ref="#/components/schemas/repository")
     *       ),
     *       @SWG\Link(link="repositoryPullRequests", ref="#/components/links/RepositoryPullRequests")
     *     )
     *   )
     * )
     *
     * @SWG\Link(link="UserRepository",
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
     * @SWG\Get(path="/2.0/repositories/{username}/{slug}/pullrequests",
     *   operationId="getPullRequestsByRepository",
     *   @SWG\Parameter(name="username",
     *     in="path",
     *     required=true,
     *     @SWG\Schema(type="string")
     *   ),
     *   @SWG\Parameter(name="slug",
     *     in="path",
     *     required=true,
     *     @SWG\Schema(type="string")
     *   ),
     *   @SWG\Parameter(name="state",
     *     in="query",
     *     @SWG\Schema(type="string",
     *       enum={"open", "merged", "declined"}
     *     )
     *   ),
     *   @SWG\Response(response=200,
     *     description="an array of pull request objects",
     *     @SWG\MediaType(mediaType="application/json",
     *       @SWG\Schema(type="array",
     *         @SWG\Items(ref="#/components/schemas/pullrequest")
     *       )
     *     )
     *   )
     * )
     *
     * @SWG\Link(link="RepositoryPullRequests",
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
     * @SWG\Get(path="/2.0/repositories/{username}/{slug}/pullrequests/{pid}",
     *   operationId="getPullRequestsById",
     *   @SWG\Parameter(name="username",
     *     in="path",
     *     required=true,
     *     @SWG\Schema(type="string")
     *   ),
     *   @SWG\Parameter(name="slug",
     *     in="path",
     *     required=true,
     *     @SWG\Schema(type="string")
     *   ),
     *   @SWG\Parameter(name="pid",
     *     in="path",
     *     required=true,
     *     @SWG\Schema(type="string")
     *   ),
     *   @SWG\Response(response=200,
     *     description="a pull request object",
     *     @SWG\MediaType(mediaType="application/json",
     *       @SWG\Schema(ref="#/components/schemas/pullrequest")
     *     ),
     *     @SWG\Link(link="pullRequestMerge", ref="#/components/links/PullRequestMerge")
     *   )
     * )
     */
    public function getPullRequestsById()
    {
    }

    /**
     * @SWG\Post(path="/2.0/repositories/{username}/{slug}/pullrequests/{pid}/merge",
     *   operationId="mergePullRequest",
     *   @SWG\Parameter(name="username",
     *     in="path",
     *     required=true,
     *     @SWG\Schema(type="string")
     *   ),
     *   @SWG\Parameter(name="slug",
     *     in="path",
     *     required=true,
     *     @SWG\Schema(type="string")
     *   ),
     *   @SWG\Parameter(name="pid",
     *     in="path",
     *     required=true,
     *     @SWG\Schema(type="string")
     *   ),
     *   @SWG\Response(response=204,
     *     description="the PR was successfully merged"
     *   )
     * )
     *
     * @SWG\Link(link="PullRequestMerge",
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




