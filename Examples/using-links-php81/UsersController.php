<?php

namespace OpenApi\Examples\UsingLinksPhp81;

use OpenApi\Attributes as OAT;

/**
 * MVC controller that handles "users/*" urls.
 */
class UsersController
{
    #[OAT\Get(path: '/2.0/users/{username}', operationId: 'getUserByName', parameters: [new OAT\Parameter(name: 'username', in: 'path', required: true, schema: new OAT\Schema(type: 'string'))], responses: [new OAT\Response(response: 200, description: 'The User', content: new OAT\JsonContent(ref: '#/components/schemas/user'), links: [new OAT\Link(link: 'userRepositories', ref: '#/components/links/UserRepositories')])])]
    public function getUserByName($username)
    {
    }
}
