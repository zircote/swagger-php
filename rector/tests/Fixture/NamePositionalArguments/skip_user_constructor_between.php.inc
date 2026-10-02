<?php

namespace OpenApi\Rector\Tests\Fixture\NamePositionalArguments;

abstract class OwnStock extends \OpenApi\Attributes\Response
{
    public function __construct(int $code, ?string $description = null)
    {
        parent::__construct(response: $code, description: $description);
    }
}

final class OwnNotFound extends OwnStock
{
    public function __construct()
    {
        // OwnStock's own signature, which the migration does not change
        parent::__construct(404, 'Not found');
    }
}
