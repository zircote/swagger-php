<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace Swagger\Annotations;

/**
 * @Annotation
 * Contact information for the exposed API.
 *
 * A "Contact Object": https://github.com/OAI/OpenAPI-Specification/blob/OpenAPI.next/versions/3.0.md#contactObject
 */
class Contact extends AbstractAnnotation
{
    /**
     * The identifying name of the contact person/organization.
     * @var string
     */
    public $name;

    /**
     * The URL pointing to the contact information.
     * @var string
     */
    public $url;

    /**
     * The email address of the contact person/organization.
     * @var string
     */
    public $email;

    /** @inheritdoc */
    public static $_types = [
        'name' => 'string',
        'url' => 'string',
        'email' => 'string'
    ];

    /** @inheritdoc */
    public static $_parents = [
        'Swagger\Annotations\Info'
    ];
}
