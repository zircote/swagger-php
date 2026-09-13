<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Undefined;

/**
 * The object provides metadata about the API.
 *
 * The metadata may be used by the clients if needed and may be presented in editing or documentation generation tools for convenience.
 *
 * @see [Info Object](https://spec.openapis.org/oas/v3.1.1.html#info-object)
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
    public $title = Undefined::UNDEFINED;

    /**
     * A short summary of the API.
     *
     * Exists as of 3.1; a 3.0 document omits it.
     *
     * @var string
     */
    public $summary = Undefined::UNDEFINED;

    /**
     * A short description of the application.
     *
     * CommonMark syntax may be used for rich text representation.
     *
     * @var string
     */
    public $description = Undefined::UNDEFINED;

    /**
     * An URL to the Terms of Service for the API.
     *
     * Must be in the format of an url.
     *
     * @var string
     */
    public $termsOfService = Undefined::UNDEFINED;

    /**
     * The contact information for the exposed API.
     *
     * @var Contact
     */
    public $contact = Undefined::UNDEFINED;

    /**
     * The license information for the exposed API.
     *
     * @var License
     */
    public $license = Undefined::UNDEFINED;

    /**
     * The version of the OpenAPI document (which is distinct from the OpenAPI Specification version or the API implementation version).
     *
     * @var string
     */
    public $version = Undefined::UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_required = ['title', 'version'];

    /**
     * @inheritdoc
     */
    public static $_types = [
        'title' => 'string',
        'summary' => 'string',
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

    public function jsonSerialize(): \stdClass
    {
        $data = parent::jsonSerialize();

        if ($this->_context->isVersion('3.0.x')) {
            unset($data->summary);
        }

        return $data;
    }
}
