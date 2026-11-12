<?php declare(strict_types=1);

namespace OpenApi\Snippets\Guide\Cookbook\Documents;

use OpenApi\Builder;

function build(): Builder
{
    return (new Builder())
        ->addSource(__DIR__ . '/models.php')
        ->withDocument(__DIR__ . '/webhooks.yaml');
}
