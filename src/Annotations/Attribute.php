<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * @Annotation
 *
 * A container for custom data to be attached to an annotation.
 * These will be ignored by swagger-php but can be used for custom processing.
 */
class Attribute extends AbstractAnnotation
{
    /**
     * @inheritdoc
     */
    public static $_parents = [
        AdditionalProperties::class,
        Components::class,
        Contact::class,
        Delete::class,
        Discriminator::class,
        Examples::class,
        ExternalDocumentation::class,
        Flow::class,
        Get::class,
        Head::class,
        Header::class,
        Info::class,
        Items::class,
        JsonContent::class,
        License::class,
        Link::class,
        MediaType::class,
        OpenApi::class,
        Operation::class,
        Options::class,
        Parameter::class,
        Patch::class,
        PathItem::class,
        Post::class,
        Property::class,
        Put::class,
        RequestBody::class,
        Response::class,
        Schema::class,
        SecurityScheme::class,
        Server::class,
        ServerVariable::class,
        Tag::class,
        Trace::class,
        Xml::class,
        XmlContent::class,
    ];
}
