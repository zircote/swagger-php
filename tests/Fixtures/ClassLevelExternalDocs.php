<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

use OpenApi\Attributes as OAT;

#[OAT\Tag(name: 'project', externalDocs: new OAT\ExternalDocumentation(url: 'https://project.example'))]
#[OAT\Tag(name: 'user', externalDocs: new OAT\ExternalDocumentation(url: 'https://user.example'))]
#[OAT\ExternalDocumentation(url: 'https://root.example')]
class ClassLevelExternalDocs
{
}
