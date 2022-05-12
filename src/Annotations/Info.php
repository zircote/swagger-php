<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * The object provides metadata about the API.
 *
 * The metadata may be used by the clients if needed and may be presented in editing or documentation generation tools for convenience.
 *
 * @see [OAI Info Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#info-object)
 *
 * @Annotation
 */
class Info extends AbstractAnnotation
{
    /**
     * The title of the application.
     *
     * @var string
     */
    public $title = Generator::UNDEFINED;

    /**
     * A short description of the application.
     *
     * CommonMark syntax may be used for rich text representation.
     *
     * @var string
     */
    public $description = Generator::UNDEFINED;

    /**
     * An URL to the Terms of Service for the API.
     *
     * Must be in the format of an url.
     *
     * @var string
     */
    public $termsOfService = Generator::UNDEFINED;

    /**
     * The contact information for the exposed API.
     *
     * @var Contact
     */
    public $contact = Generator::UNDEFINED;

    /**
     * The license information for the exposed API.
     *
     * @var License
     */
    public $license = Generator::UNDEFINED;

    /**
     * The version of the OpenAPI document (which is distinct from the OpenAPI Specification version or the API implementation version).
     *
     * @var string
     */
    public $version = Generator::UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_required = ['title', 'version'];

    /**
     * @inheritdoc
     */
    public static $_types = [
        'title' => 'string',
        'version' => 'string',
        'description' => 'string',
        'termsOfService' => 'string',
    ];

    /**
     * @inheritdoc
     */
    public static $_nested = [
        Contact::class => 'contact',
        License::class => 'license',
        Attachable::class => ['attachables'],
    ];

    /**
     * @inheritdoc
     */
    public static $_parents = [
        OpenApi::class,
    ];
}
