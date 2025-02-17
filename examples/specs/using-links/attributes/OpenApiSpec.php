<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingLinks\Attributes;

use OpenApi\Attributes as OAT;

#[OAT\Info(
    version: '1.0.0',
    description: 'Using links',
    title: 'Link Example',
    contact: new OAT\Contact(
        name: 'Contact Name',
        email: 'support@example.com'
    )
)]
class OpenApiSpec
{
}
