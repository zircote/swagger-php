<?php

namespace OpenApi\Examples\UsingLinksPhp81;

use OpenApi\Attributes as OAT;

#[OAT\Info(
    version: '1.0.0',
    description: '',
    title: 'Link Example',
    contact: new OAT\Contact(
        name: 'Contact Name',
        email: 'support@example.com'
    )
)]
class OpenApiSpec
{
}
