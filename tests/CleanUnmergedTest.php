<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

use Swagger\Analysis;
use Swagger\Annotations\Contact;
use Swagger\Annotations\License;
use Swagger\Processors\CleanUnmerged;
use Swagger\Processors\MergeIntoOpenApi;

class CleanUnmergedTest extends SwaggerTestCase
{
    public function testCleanUnmergedProcessor()
    {
        $comment = <<<END
@OAS\Info(
    title="Info only has one contact field.",
    version="test",
)
@OAS\License(
    name="MIT",
    @OAS\Contact(
        name="Batman"
    )
)

END;
        $analysis = new Analysis($this->parseComment($comment));
        $this->assertCount(3, $analysis->annotations);
        $analysis->process(new MergeIntoOpenApi());
        $this->assertCount(4, $analysis->annotations);
        $before = $analysis->split();
        $this->assertCount(2, $before->merged->annotations, 'Generated @OAS\OpenApi and @OAS\Info');
        $this->assertCount(2, $before->unmerged->annotations, '@OAS\License + @OAS\Contact');
        $this->assertCount(0, $analysis->openapi->_unmerged);
        $analysis->validate(); // Validation fails to detect the unmerged annotations.

        // CleanUnmerged should place the unmerged annotions into the swagger->_unmerged array.
        $analysis->process(new CleanUnmerged());
        $between = $analysis->split();
        $this->assertCount(2, $between->merged->annotations, 'Generated @OAS\OpenApi and @OAS\Info');
        $this->assertCount(2, $between->unmerged->annotations, '@OAS\License + @OAS\Contact');
        $this->assertCount(2, $analysis->openapi->_unmerged); // 1 would also be oke, Could a'Only the @OAS\License'
        $this->assertSwaggerLogEntryStartsWith('Unexpected @OAS\License(), expected to be inside @OAS\Info in ');
        $this->assertSwaggerLogEntryStartsWith('Unexpected @OAS\Contact(), expected to be inside @OAS\Info in ');
        $analysis->validate();

        // When a processor places a previously unmerged annotation into the swagger obect.
        $license = $analysis->getAnnotationsOfType(License::class)[0];
        $contact = $analysis->getAnnotationsOfType(Contact::class)[0];
        $analysis->openapi->info->contact = $contact;
        $this->assertCount(1, $license->_unmerged);
        $analysis->process(new CleanUnmerged());
        $this->assertCount(0, $license->_unmerged);
        $after = $analysis->split();
        $this->assertCount(3, $after->merged->annotations, 'Generated @OAS\OpenApi, @OAS\Info and @OAS\Contact');
        $this->assertCount(1, $after->unmerged->annotations, '@OAS\License');
        $this->assertCount(1, $analysis->openapi->_unmerged);
        $this->assertSwaggerLogEntryStartsWith('Unexpected @OAS\License(), expected to be inside @OAS\Info in ');
        $analysis->validate();
    }
}
