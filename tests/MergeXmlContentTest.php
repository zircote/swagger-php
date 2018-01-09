<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

use Swagger\Analysis;
use Swagger\Annotations\Response;
use Swagger\Processors\MergeXmlContent;

class MergeXmlContentTest extends SwaggerTestCase
{
    public function testXmlContent()
    {
        $comment = <<<END
        @OAS\Response(response=200,
            @OAS\XmlContent(type="array",
                @OAS\Items(ref="#/components/schemas/repository")
            )
        )
END;
        $analysis = new Analysis($this->parseComment($comment));
        $this->assertCount(3, $analysis->annotations);
        $response = $analysis->getAnnotationsOfType(Response::class)[0];
        $this->assertNull($response->content);
        $this->assertCount(1, $response->_unmerged);
        $analysis->process(new MergeXmlContent());
        $this->assertCount(1, $response->content);
        $this->assertCount(0, $response->_unmerged);
        $json = json_decode(json_encode($response), true);
        $this->assertSame('#/components/schemas/repository', $json['content']['application/xml']['schema']['items']['$ref']);
    }

    public function testMultipleMediaTypes()
    {
        $comment = <<<END
        @OAS\Response(response=200,
            @OAS\MediaType(mediaType="image/png"),
            @OAS\XmlContent(type="array",
                @OAS\Items(ref="#/components/schemas/repository")
            )
        )
END;
        $analysis = new Analysis($this->parseComment($comment));
        $response = $analysis->getAnnotationsOfType(Response::class)[0];
        $this->assertCount(1, $response->content);
        $analysis->process(new MergeXmlContent());
        $this->assertCount(2, $response->content);
    }
}
