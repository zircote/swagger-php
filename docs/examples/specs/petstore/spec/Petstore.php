<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Petstore\Spec;

use OpenApi\Spec as OA;

#[OA\OpenApi(version: '3.1.0')]
#[OA\Info(title: 'Swagger Petstore', description: "This is a sample Petstore server.\nYou can find out more about Swagger at [http://swagger.io](http://swagger.io)\nor on [irc.freenode.net, #swagger](http://swagger.io/irc/).", termsOfService: 'http://swagger.io/terms/', version: '1.0.0', contact: new OA\Contact(email: 'apiteam@swagger.io'), license: new OA\License(
    name: 'Apache 2.0',
    url: 'http://www.apache.org/licenses/LICENSE-2.0.html'
))]
#[OA\Server(url: 'https://petstore.com/1.0.0', description: 'Petstore API')]
#[OA\Tag(
    name: 'pet',
    description: 'Everything about your Pets',
    externalDocs: new OA\ExternalDocumentation(url: 'http://swagger.io', description: 'Find out more'),
)]
#[OA\Tag(name: 'store', description: 'Access to Petstore orders')]
#[OA\Tag(
    name: 'user',
    description: 'Operations about user',
    externalDocs: new OA\ExternalDocumentation(url: 'http://swagger.io', description: 'Find out more about store'),
)]
#[OA\ExternalDocumentation(url: 'http://swagger.io', description: 'Find out more about Swagger')]
class Petstore
{
}
