<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Petstore\Attributes;

use OpenApi\Attributes as OAT;

#[OAT\OpenApi(
    info: new OAT\Info(
        description: 'This is a sample Petstore server.
You can find out more about Swagger at [http://swagger.io](http://swagger.io)
or on [irc.freenode.net, #swagger](http://swagger.io/irc/).',
        version: '1.0.0',
        title: 'Swagger Petstore',
        termsOfService: 'http://swagger.io/terms/',
        contact: new OAT\Contact(email: 'apiteam@swagger.io'),
        license: new OAT\License(
            name: 'Apache 2.0',
            url: 'http://www.apache.org/licenses/LICENSE-2.0.html'
        )
    ),
    tags: [
        new OAT\Tag(
            name: 'pet',
            description: 'Everything about your Pets',
            externalDocs: new OAT\ExternalDocumentation(
                description: 'Find out more',
                url: 'http://swagger.io'
            )
        ),
        new OAT\Tag(
            name: 'store',
            description: 'Access to Petstore orders'
        ),
        new OAT\Tag(
            name: 'user',
            description: 'Operations about user',
            externalDocs: new OAT\ExternalDocumentation(
                description: 'Find out more about store',
                url: 'http://swagger.io'
            )
        ),
    ],
    servers: [
        new OAT\Server(
            description: 'Petstore API',
            url: 'https://petstore.com/1.0.0'
        ),
    ],
    externalDocs: new OAT\ExternalDocumentation(
        description: 'Find out more about Swagger',
        url: 'http://swagger.io'
    )
)]
class Petstore
{
}
