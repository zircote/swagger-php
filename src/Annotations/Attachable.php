<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

/**
 * A container for custom data to be attached to an annotation.
 *
 * These will be ignored by `swagger-php` but can be used for custom processing.
 *
 * @Annotation
 */
class Attachable extends AbstractAnnotation
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
        PathParameter::class,
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
        Webhook::class,
        Xml::class,
        XmlContent::class,
    ];

    /**
     * Allows to type-hint a specific parent annotation class.
     *
     * Container to allow custom annotations that are limited to a subset of potential parent
     * annotation classes.
     *
     * @return array<class-string>|null List of valid parent annotation classes. If `null`, the default nesting rules apply.
     */
    public function allowedParents(): ?array
    {
        return null;
    }
}
