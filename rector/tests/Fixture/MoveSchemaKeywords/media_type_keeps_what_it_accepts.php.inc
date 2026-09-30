<?php

namespace OpenApi\Rector\Tests\Fixture\MoveSchemaKeywords;

class MediaTypeKeepsWhatItAccepts
{
    #[\OpenApi\Spec\Response(response: 200, description: 'ok', content: new \OpenApi\Spec\MediaType\Json(type: 'object', nullable: true))]
    public function get(): void
    {
    }
}

?>
-----
<?php

namespace OpenApi\Rector\Tests\Fixture\MoveSchemaKeywords;

class MediaTypeKeepsWhatItAccepts
{
    #[\OpenApi\Spec\Response(response: 200, description: 'ok', content: new \OpenApi\Spec\MediaType\Json(type: 'object', schema: new \OpenApi\Spec\Schema(nullable: true)))]
    public function get(): void
    {
    }
}

?>
