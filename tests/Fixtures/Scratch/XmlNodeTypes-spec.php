<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

/**
 * XML node types, new in OpenAPI 3.2, rendering for example something like:
 *
 *   <person id="42">
 *     <name>Jane</name>
 *     <tags><tag>admin</tag></tags>
 *     <bio><![CDATA[Likes <b>bold</b> text]]></bio>
 *     <salary currency="EUR">1200.5</salary>
 *   </person>
 *
 * The 3.0 and 3.1 documents are the assertion that matters as much as the 3.2 one: an attribute
 * and a wrapped list are written with `attribute` and `wrapped`, and the CDATA and text nodes,
 * which have no older form, are left out with a warning each.
 */
#[OA\Info(title: 'Xml node types', version: '1.0')]
#[OA\Operation\Get(path: '/people/{id}', operationId: 'getPerson')]
#[OA\Parameter\Path(name: 'id', schema: new OA\Schema(type: 'string'))]
#[OA\Response(response: 200, description: 'The person', content: new OA\MediaType\Xml(schema: new OA\Schema(ref: '#/components/schemas/Person')))]
class XmlNodeTypesControllerSpec
{
}

#[OA\Schema(
    component: 'Person',
    type: 'object',
    xml: new OA\Xml(name: 'person'),
    properties: [
        'id' => new OA\Schema(type: 'string', xml: new OA\Xml(nodeType: OA\XmlNodeType::Attribute)),
        'name' => new OA\Schema(type: 'string'),
        'tags' => new OA\Schema(
            type: 'array',
            items: new OA\Schema(type: 'string', xml: new OA\Xml(name: 'tag')),
            xml: OA\XmlNodeType::Element,
        ),
        'bio' => new OA\Schema(type: 'string', xml: OA\XmlNodeType::Cdata),
        'salary' => new OA\Schema(ref: '#/components/schemas/Price'),
    ],
)]
class XmlNodeTypesPersonSpec
{
}

#[OA\Schema(
    component: 'Price',
    type: 'object',
    properties: [
        'currency' => new OA\Schema(type: 'string', xml: new OA\Xml(nodeType: OA\XmlNodeType::Attribute)),
        'amount' => new OA\Schema(type: 'number', xml: OA\XmlNodeType::Text),
    ],
)]
class XmlNodeTypesPriceSpec
{
}
